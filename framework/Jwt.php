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
 * @version 1.0.0
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
     * @var mixed
     */
    protected $error;

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
     * @return mixed
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = '';
        return $error;
    }

    /**
     * 注册配置信息
     *
     * @param array $config
     * @return void
     */
    public function register(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 创建JWT
     *
     * @param int|string $uid   面向的用户ID
     * @param array $ext        扩展的JWT内容
     * @return mixed
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
     * @return mixed
     */
    public function check($jwt)
    {
        try {
            $token = new Token;
            return $token->check($jwt, $this->config['key'], $this->config['alg']);
        } catch (JwtException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
