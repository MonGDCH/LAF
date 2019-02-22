<?php
namespace Laf\lib;

use FApi\Container;
use Laf\util\Strs;

/**
 * 权限控制管理, JWT简易实现OAuath1.0
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class OAuth
{
	/**
	 * 权限配置信息
	 *
	 * @var [type]
	 */
	protected $config = [
		// ticket加密盐
		'salt'		=> 'mon123465789',
		// token key值
		'token_key'	=> 'mon_fapi_key',
		// token有效期限
		'token_life'=> 86400,
	];

	/**
	 * 错误信息
	 *
	 * @var [type]
	 */
	protected $error;

	/**
	 * 覆盖配置
	 *
	 * @param array $config [description]
	 */
	public function __construct(array $config = [])
	{
		$config = empty($config) ? Container::get('config')->get('oauth', []) : $config;
		$this->config = array_merge($this->config, $config);
	}

	/**
	 * 获取错误信息
	 *
	 * @return [type] [description]
	 */
	public function getError()
	{
		$error = $this->error;
		$this->error = '';
		return $error;
	}

	/**
	 * 创建token
	 *
	 * @param  [type] $user [description]
	 * @param  [type] $key  [description]
	 * @return [type]       [description]
	 */
	public function createToken($user, $key = null)
	{
		$key = !is_null($key) ? $key : $this->config['token_key'];
		
		$info = [
			'user'  => $user,
            'ctime' => $_SERVER['REQUEST_TIME'],
            'sign'	=> $this->sign($key, $user, $_SERVER['REQUEST_TIME'])
		];

		return $this->createTicket(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 校验token
	 *
	 * @param  [type] $token token值
	 * @param  [type] $key   token加密key
	 * @param  [type] $life  token有效时间
	 * @return [type]        [description]
	 */
	public function checkToken($token, $key = null, $life = null)
	{
		$key = $key ? $key : $this->config['token_key'];
        $life = !is_null($life) && is_numeric($life) ? $life : $this->config['token_life'];

		$ticket = $this->parseTicket($token);
        $info = json_decode($ticket, true);
        if(!$info || !isset($info['user']) || !isset($info['ctime']) || !isset($info['sign']) || $this->sign($key, $info['user'], $info['ctime']) != $info['sign']){
            $this->error = '无效的Token';
            return false;
        }
        elseif(($_SERVER['REQUEST_TIME'] - $info['ctime']) > $life ){
            $this->error = 'token已过期';
            return false;
        }

		return $info;
	}

	/**
	 * 创建ticket
	 *
	 * @param  string $info 唯一标志信息
	 * @return [type]     	[description]
	 */
	public function createTicket($info)
	{
		return strtoupper(bin2hex($this->encryption($info)));
	}

	/**
	 * 解密ticket
	 *
	 * @param  string $ticket [description]
	 * @return [type]         [description]
	 */
	public function parseTicket($ticket)
	{
        if(strlen($ticket) % 2 != 0){
            $this->error = '错误的Token';
            return false;
        }
        $parseToken = hex2bin($ticket);
        if(!$parseToken){
            $this->error = '解析Token失败';
            return false;
        }
		return $this->decryption($parseToken);
	}

	/**
	 * 加密信息
	 *
	 * @param  [type] $str 加密的字符串
	 * @return [type]      [description]
	 */
    protected function encryption($str)
    {
        $res = Strs::stringEncrypt($str, $this->config['salt'], $this->config['token_life']);
        return base64_encode($res);
    }

   	/**
   	 * 解密信息
   	 *
   	 * @param  [type] $str 解密的字符串
   	 * @return [type]      [description]
   	 */
    protected function decryption($str)
    {
        $res = Strs::stringDecrypt(base64_decode($str), $this->config['salt']);
        return $res;
    }

    /**
     * 数据签名
     *
     * @param  [type] $key   加密key
     * @param  [type] $user  用户标识
     * @param  [type] $ctime 创建时间,作为随机因子
     * @return [type]        [description]
     */
    protected function sign($key, $user, $ctime)
    {
    	return md5($key . $user . $ctime);
    }
}