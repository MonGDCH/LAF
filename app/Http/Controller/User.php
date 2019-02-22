<?php
namespace App\Http\Controller;

use Laf\Controller;
use App\Model\User as userModel;

/**
 * 用户相关控制器
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class User extends Controller
{
	/**
	 * 操作模型
	 *
	 * @var [type]
	 */
	protected $userModel;

	/**
	 * 构造方法
	 */
	public function __construct()
	{
		parent::__construct();

		$this->userModel = new userModel;
	}

	/**
	 * 用户注册
	 *
	 * @return [type] [description]
	 */
	public function register()
	{
		$option = $this->request->post();
		$register = $this->userModel->register($option);
		if(!$register){
			return $this->errorJson($this->userModel->getError());
		}		

		return $this->successjson('success');
	}

	/**
	 * 忘记密码
	 *
	 * @return [type] [description]
	 */
	public function forget()
	{
		$option = $this->request->post();
		$forget = $this->userModel->forget($option);
		if(!$forget){
			return $this->errorJson($this->userModel->getError());
		}		
		// 已发送修改密码邮件，请注意查收
		return $this->successjson('the modified password email has been sent, please check');
	}

	/**
	 * 用户登录
	 *
	 * @return [type] [description]
	 */
	public function login()
	{
		$option = $this->request->post();
		$login = $this->userModel->login($option);
		if(!$login){
			return $this->errorJson($this->userModel->getError());
		}		

		$userInfo = $this->container->make('session')->get('userInfo');
		unset($userInfo['password']);
		unset($userInfo['salt']);
		unset($userInfo['status']);
		unset($userInfo['update_time']);

		return $this->successjson('success', $userInfo);
	}

	/**
	 * 用户登出
	 *
	 * @return [type] [description]
	 */
	public function logout()
	{
		$this->container->make('session')->del('userInfo');
		return $this->successjson('success');
	}
}