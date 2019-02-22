<?php
namespace App\Validate;

use Laf\lib\Validate;
use FApi\Container;

/**
 * 自定义帐号验证器
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Account extends Validate
{
	/**
     * 验证规则
     *
     * @var [type]
     */
    public $rule = [
        'pid'           => 'required|int|min:1',
        'ip'            => 'ip',
        'port'			=> 'int|min:8000|max:25000',
        'encrypt'       => 'required',
        'account'       => 'required',
        'password'      => 'required',
        'total_flow'    => 'num|min:0',
        'use_flow'      => 'num|min:0',
        'status'        => 'required|int|min:0',
        'invite_code'   => 'required',
    ];

    /**
     * 错误提示信息
     *
     * @var [type]
     */
    public $message = [
        'pid'           => 'params error',
        'ip'            => 'ip format error',
        'port'          => 'port format error',
        'account'       => 'account required',
        'password'      => 'password required',
        'encrypt'       => 'encrypt required',
        'total_flow'    => 'total_flow format error',
        'use_flow'      => 'use_flow format error',
        'status'        => 'status format error',
        'invite_code'   => 'invite code required',
    ];

    /**
     * 验证场景
     * @var [type]
     */
    public $scope = [
        // 新增
        'add'       => ['account', 'password', 'ip', 'port', 'encrypt', 'total_flow', 'use_flow', 'status', 'invite_code'],
        // 修改
        'modify'    => ['pid', 'ip', 'port', 'account', 'password', 'encrypt', 'total_flow', 'use_flow', 'status'],
        // 修改密码
        'modify_pwd'=> ['pid', 'password'],
    ];

}