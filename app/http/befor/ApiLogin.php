<?php

namespace app\http\befor;

use Laf\Jwt;
use FApi\Url;
use FApi\Request;
use mon\util\Tool;

/**
 * API访问中间件，验证是否具有调用权限
 */
class ApiLogin
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
        if (!Tool::instance()->is_wx()) {
            return Url::instance()->abort(404);
        }
        $token = Request::instance()->server('HTTP_MON_TOKEN');
        if (!$token) {
            return Url::instance()->abort(403);
        }
        $info = Jwt::instance()->check($token);
        if (!$info) {
            // 判断token是否过期无效，过期无效则返回状态码800，告知刷新token
            if (Jwt::instance()->getErrorCode() == 12) {
                // 状态码过期
                return Url::instance()->result(401, 'Token Expiration');
            }
            return Url::instance()->abort(401);
        }

        $val['uid'] = $info['sub'];
        $app->setVars($val);

        return true;
    }
}
