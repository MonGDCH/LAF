<?php

namespace app\http\befor;

use FApi\Url;
use Laf\Session;

/**
 * UserLogin 前置中间件，验证用户登录态
 *
 * Class UserLogin
 * @version 1.0.0
 */
class UserLogin
{
    /**
     * 回调方法
     *
     * @param mixed $val 依赖参数
     * @param \FApi\App $app APP实例
     * @return boolean 返回true执行后续操作
     */
    public function handler($val, $app)
    {
        $userInfo = Session::instance()->get('user_info');
        $loginURL = Url::instance()->build('/login');
        if (!$userInfo) {
            return Url::instance()->redirect($loginURL);
        }

        return true;
    }
}
