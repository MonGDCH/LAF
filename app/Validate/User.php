<?php
namespace App\Validate;

use Laf\lib\Validate;
use FApi\Container;

/**
 * 自定义用户验证器
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class User extends Validate
{
	/**
     * 验证规则
     *
     * @var [type]
     */
    public $rule = [
        'uid'           => 'required|int|min:0',
        'username'      => 'maxLength:12|minLength:3',
        'email'			=> 'email',
        'moble'         => 'moble',
        'password'      => 'minLength:8|maxLength:16|language',
        'invite'        => 'required',
        'verify'        => 'verify',
        'status'        => 'required|int|min:0'
    ];

    /**
     * 错误提示信息
     *
     * @var [type]
     */
    public $message = [
        'uid'           => 'params error',
        'username'      => 'username format error',
        'password'      => 'password format error',
        'moble'         => 'moble format error',
        'email'    		=> 'email format error',
        'invite'        => 'invite required',
        'verify'        => 'verify code error',
        'status'        => 'status format error',
    ];

    /**
     * 验证场景
     * @var [type]
     */
    public $scope = [
        // 注册
        'register'      => ['email', 'password', 'username', 'moble', 'invite', 'verify'],
        // 登录
        'login'         => ['email', 'password', 'verify'],
        // 发送短信
        'forget'        => ['email', 'verify'],
        // 冻结用户
        'del'           => ['uid', 'status'],
    ];

    /**
     * 验证图像验证码
     *
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function verify($value)
    {
        $code = Container::instance()->session->get('verify', '');
        if(empty($code) || empty($value)){
            return false;
        }

        $check = (strtolower($code) == strtolower($value));
        return $check;
    }
}