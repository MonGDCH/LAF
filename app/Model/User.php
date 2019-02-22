<?php
namespace App\Model;

use mon\Model;
use Laf\util\Strs;
use FApi\Container;
use FApi\traits\Instance;
use App\Validate\User as userValidate;
use App\Model\Invite;
use App\Model\Account;

/**
 * 用户模型
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class User extends Model
{
	use Instance;

	/**
	 * 模型操作表
	 *
	 * @var string
	 */
	protected $table = 'pz_user';

	/**
	 * 验证器
	 *
	 * @var [type]
	 */
	protected $validate;

	/**
     * 新增自动写入字段
     * @var [type]
     */
    protected $insert = ['create_time', 'update_time'];

    /**
     * 更新自动写入字段
     * @var [type]
     */
    protected $update = ['update_time'];

	/**
	 * 构造方法
	 */
	public function __construct()
	{
		$this->validate = new userValidate;
	}

	/**
	 * 判断用户是否存在
	 *
	 * @param  [type] $value 值
	 * @param  string $key   索引
	 * @return [type]        [description]
	 */
	public function getInfo($value, $key = 'email')
	{
		$info = $this->where($key, $value)->find();
		if(!$info){
			$this->error = 'user not exists';
			return false;
		}
		return $info;
	}

	/**
	 * 用户注册
	 *
	 * @param  [type] $option [description]
	 * @return [type]         [description]
	 */
	public function register($option)
	{
		$check = $this->validate->data($option)->scope('register')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}

		// 用户已存在
		if($this->getInfo($option['email'])){
			$this->error = 'email exists';
			return false;
		}
		if($this->getInfo($option['username'], 'username')){
			$this->error = 'username exists';
			return false;
		}

		// 验证邀请码
		$inviteInfo = Invite::instance()->getInfo($option['invite']);
		if(!$inviteInfo){
			$this->error = 'invitation code error';
			return false;
		}
		if($inviteInfo['isuse'] == '1' || $inviteInfo['uid'] != '0'){
			$this->error = 'invitation code unusable';
			return false;
		}

		$info = [
			'username'	=> $option['username'],
			'email'		=> $option['email'],
			'moble'		=> $option['moble'],
			'salt'		=> Strs::randString(),
		];
		$info['password'] = $this->enPassWord($option['password'], $info['salt']);

		// 注册，分配vps帐号
		$this->startTrans();
		try{
			// 创建用户
			$uid = $this->save($info, null, 'id');
			Container::get('log')->sql('create user => ' . $this->getLastSql());
			if(!$uid){
				$this->rollback();
				$this->error = 'register failed';
				return false;
			}

			// 标记邀请码
			$mark = Invite::instance()->where('id', $inviteInfo['id'])->where('isuse', 0)->update(['isuse' => 1, 'uid' => $uid]);
			Container::get('log')->sql('create user => ' . Invite::instance()->getLastSql());
			if(!$mark){
				$this->rollback();
				$this->error = 'mark invite failed';
				return false;
			}

			// 判断是否存在vps帐号绑定，存在则分配
			if($inviteInfo['pid'] != '0'){
				$allot = Account::instance()->allot($uid, $inviteInfo['pid']);
				if(!$allot){
					$this->rollback();
					$this->error = Account::instance()->getError();
					return false;
				}
			}

			$this->commit();
			return true;
		}
		catch(\Exception $e){
			$this->rollback();
			$this->error = 'register exception';
			return false;
		}
	}

	/**
	 * 忘记密码
	 *
	 * @param  [type] $option [description]
	 * @return [type]         [description]
	 */
	public function forget($option)
	{
		$check = $this->validate->data($option)->scope('forget')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}
		// 用户是不存在
		if(!$this->getInfo($option['email'])){
			return false;
		}
		
		// TODO 发送修改密码邮件
		
		// $mail = new PHPMailer(); // create a new object
		// $mail->IsSMTP(); // enable SMTP
		// $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		// $mail->SMTPAuth = true; // authentication enabled
		// $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		// $mail->Host = "smtp.gmail.com";
		// $mail->Port = 465; // or 587
		// $mail->IsHTML(true);
		// $mail->Username = "2019onlyyou@gmail.com";
		// $mail->Password = "laoqi5311725aszp";
		// $mail->SetFrom("2019onlyyou@gmail.com");
		// $mail->Subject = "Test";
		// $mail->Body = "hello";
		// $mail->AddAddress($option['email']);

		// if(!$mail->Send()) {
		//     echo "Mailer Error: " . $mail->ErrorInfo;
		// } else {
		//     echo "Message has been sent";
		// }
		// exit();
		
		return true;
	}

	/**
	 * 用户登录
	 *
	 * @param  [type] $option [description]
	 * @return [type]         [description]
	 */
	public function login($option)
	{
		$check = $this->validate->data($option)->scope('login')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}

		// 获取用户信息
		$userInfo = $this->getInfo($option['email']);
		if(!$userInfo){
			return false;
		}
		// 判断是否有效用户
		if($userInfo['status'] != '1'){
			$this->error = 'account frozen';	// 帐号已冻结
			return false;
		}
		// 验证密码
		if($userInfo['password'] != $this->enPassWord($option['password'], $userInfo['salt'])){
			$this->error = 'wrong password'; 	// 密码错误
		}

		// 记录session
		Container::get('session')->set('userInfo', $userInfo);
		return true;
	}

	/**
	 * 停启用用户
	 *
	 * @param  [type] $option [description]
	 * @return [type]         [description]
	 */
	public function del($option)
	{
		$check = $this->validate->data($option)->scope('del')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}

		$save = $this->save(['status' => $option['status']], ['id' => $option['uid']]);
		if(!$save){
			$this->error = 'modify user status error';
			return false;
		}

		return true;
	}

	/**
	 * 加密密码
	 *
	 * @param  [type] $password 原密码
	 * @param  [type] $salt     盐
	 * @return [type]           [description]
	 */
	public function enPassWord($password, $salt)
	{
		return md5($password . $salt);
	}

	/**
	 * 自动完成create_time字段
	 * 
	 * @param [type] $val 默认值
	 * @param array  $row 列值
	 */
	protected function setCreateTimeAttr($val)
	{
		return $_SERVER['REQUEST_TIME'];
	}

	/**
	 * 自动完成update_time字段
	 * 
	 * @param [type] $val 默认值
	 * @param array  $row 列值
	 */
	protected function setUpdateTimeAttr($val)
	{
		return $_SERVER['REQUEST_TIME'];
	}
}