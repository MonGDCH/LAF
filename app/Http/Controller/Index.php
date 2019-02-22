<?php
namespace App\Http\Controller;

use Laf\Controller;
use Laf\util\Strs;
use App\Model\User;
use App\Libs\OAuth;

class Index extends Controller
{
	/**
	 * 用户模型
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
		$this->userModel = User::instance();
	}

	/**
	 * index方法
	 *
	 * @return [type] [description]
	 */
	public function index()
	{
		return 123456;
	}

	/**
	 * 验证码
	 *
	 * @return [type] [description]
	 */
	public function verify()
	{
		$code = Strs::randString(4);
		$this->container->session->set('verify', $code);
		return $this->container->captcha->create($code);
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
		$userInfo['token'] = OAuth::get('api')->createToken($userInfo['id']);

		return $this->successJson('Success', $userInfo);
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

		return $this->successJson('Success');
	}

	/**
	 * 重置密码
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

		return $this->successJson('Success');
	}
}