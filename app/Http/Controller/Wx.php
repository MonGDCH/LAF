<?php
namespace App\Http\Controller;

use Laf\lib\Wx as WxApi;
use Laf\Controller;
use App\Model\Admin;

/**
 * 微信相关接口控制器
 */
class Wx extends  Controller
{
	/**
	 * Appid
	 * 
	 * @var string
	 */
	private $appid;

	/**
	 * 秘钥
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * 构造方法
	 */
	public function __construct()
	{
		parent::__construct();
		$this->appid = $this->container->get('config')->get('wxApp.appid');
		$this->secret = $this->container->get('config')->get('wxApp.secret');
	}

	/**
	 * 用户登录，获取openid及google_secret
	 *
	 * @param  [type] $code [description]
	 * @return [type]       [description]
	 */
	public function login($code)
	{
		$wxApi = new WxApi($this->appid, $this->secret);
		$openData = $wxApi->getOpenid($code);
		if(!$openData){
			return $this->errorJson($wxApi->getError());
		}
		$openid = $openData['openid'];
		$adminModel = new Admin();
		$secret = $adminModel->getSecret($openid);
		$secret = ($secret == false) ? '' : $secret;

		return $this->successJson('OK', ['openid' => $openid, 'secret' => $secret]);
	}

	/**
	 * 获取Secret
	 *
	 * @return [type] [description]
	 */
	public function getSecret()
	{
		$openid = $this->request->post('openid', null);
		if(!$openid){
			return $this->errorJson('params invald!');
		}

		$adminModel = new Admin();
		$secret = $adminModel->getSecret($openid);
		if(!$secret){
			return $this->errorJson($adminModel->getError());
		}

		return $this->successJson('OK', ['secret' => $secret]);
	}
}