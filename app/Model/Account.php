<?php
namespace App\Model;

use mon\Model;
use Laf\util\Strs;
use FApi\Container;
use FApi\traits\Instance;
use App\Validate\Account as AccountValidate;
use App\Model\Invite;

/**
 * 用户模型
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Account extends Model
{
	use Instance;

	/**
	 * 模型操作表
	 *
	 * @var string
	 */
	protected $table = 'pz_account';

	/**
	 * 验证器
	 *
	 * @var [type]
	 */
	protected $validate;

	/**
     * 新增自动写入字段
     *
     * @var [type]
     */
    protected $insert = ['create_time', 'update_time'];

    /**
     * 更新自动写入字段
     *
     * @var [type]
     */
    protected $update = ['update_time'];

    /**
     * 构造方法
     */
    public function __construct()
    {
    	$this->validate = new AccountValidate;
    }

    /**
     * 获取信息
     *
     * @return [type] [description]
     */
    public function getInfo($value, $key = 'id')
	{
		$info = $this->where($key, $value)->find();
		if(!$info){
			$this->error = 'invite not exists';
			return false;
		}

		return $info;
	}

	/**
	 * 分配帐号
	 *
	 * @param  [type] $uid [description]
	 * @param  [type] $pid [description]
	 * @return [type]      [description]
	 */
	public function allot($uid, $pid)
	{
		$account = $this->getInfo($pid);
		if(!$account){
			return false;
		}

		$allot = $this->save(['uid' => $uid], ['id' => $pid]);
		if(!$allot){
			$this->error = 'allot account error';
			return false;
		}

		return true;
	}

	/**
	 * 增加帐号
	 *
	 * @param [type] $option [description]
	 */
	public function add($option)
	{
		$check = $this->validate->data($option)->scope('add')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}

		$info = [
			'ip'            => $option['ip'],
	        'port'          => $option['port'],
	        'account'       => $option['account'],
	        'password'      => $option['password'],
	        'encrypt'       => $option['encrypt'],
	        'total_flow'    => $option['total_flow'],
	        'use_flow'      => $option['use_flow'],
	        'status'        => $option['status']
		];

		$this->startTrans();
		try{
			// 保存帐号
			$pid = $this->save($info, null, 'id');
			if(!$pid){
				$this->rollback();
				$this->error = 'add account error';
				return false;
			}

			$invite = Invite::instance()->add($option['code'], $pid);
			if(!$invite){
				$this->rollback();
				$this->error = Invite::instance()->getError();
				return false;
			}

			$this->commit();
			return true;
		}
		catch(\Exception $e){
			$this->rollback();
			$this->error = 'add account exception';
			return false;
		}
	}

	/**
	 * 后台修改帐号
	 * 
	 * @param  [type] $option [description]
	 * @return [type]         [description]
	 */
	public function modify($option)
	{
		$check = $this->validate->data($option)->scope('modify')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}

		$info = [
			'ip'            => $option['ip'],
	        'port'          => $option['port'],
	        'account'       => $option['account'],
	        'password'      => $option['password'],
	        'encrypt'       => $option['encrypt'],
	        'total_flow'    => $option['total_flow'],
	        'use_flow'      => $option['use_flow'],
	        'status'        => $option['status']
		];

		$save = $this->save($info, ['id' => $option['pid']]);
		if(!$save){
			$this->error = 'modify account error';
			return false;
		}

		return true;
	}

	/**
	 * 前台修改密码
	 *
	 * @param  [type] $option [description]
	 * @param  [type] $uid    [description]
	 * @return [type]         [description]
	 */
	public function modifyPwd($option, $uid)
	{
		$check = $this->validate->data($option)->scope('modify_pwd')->check();
		if($check !== true){
			$this->error = $check;
			return false;
		}
		// 验证帐号所有权
		$info = $this->where('uid', $uid)->where('id', $option['pid'])->find();
		if(!$info){
			$this->error = 'params invald';
			return false;
		}

		// TODO 记录修改操作表
		 
		return true;
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