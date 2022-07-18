<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\auth\jwt\Token;
use mon\auth\jwt\Payload;
use mon\auth\exception\JwtException;

/**
 * JWT权限控制
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.1 优化注解  2022-07-15
 */
class Jwt
{
    use Instance;

    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [];

    /**
     * 错误信息
     *
     * @var string
     */
    protected $error;

    /**
     * 错误码
     *
     * @var integer
     */
    protected $errorCode;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->config = Config::instance()->get('app.jwt', []);
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = '';
        return $error;
    }

    /**
     * 获取错误码
     *
     * @return integer
     */
    public function getErrorCode()
    {
        $code = $this->errorCode;
        $this->errorCode = 0;
        return $code;
    }

    /**
     * 注册配置信息
     *
     * @param array $config
     * @return Jwt
     */
    public function register(array $config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * 创建JWT
     *
     * @param int|string $uid   面向的用户ID
     * @param array $ext        扩展的JWT内容
     * @return string|false
     */
    public function create($uid, array $ext = [])
    {
        try {
            $build = new Payload;
            $payload = $build->setIss($this->config['iss'])->setSub($uid)->setExt($ext)->setExp($this->config['exp']);

            $token = new Token;
            return $token->create($payload, $this->config['key'], $this->config['alg']);
        } catch (JwtException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 验证JWT
     *
     * @param string $jwt   JWT字符串
     * @return array|false
     */
    public function check($jwt)
    {
        try {
            $token = new Token;
            return $token->check($jwt, $this->config['key'], $this->config['alg']);
        } catch (JwtException $e) {
            $this->error = $e->getMessage();
            $this->errorCode = $e->getCode();
            return false;
        }
    }
}
