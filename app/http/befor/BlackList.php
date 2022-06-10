<?php

namespace app\http\befor;

use FApi\Url;
use FApi\Request;
use mon\util\Tool;
use mon\env\Config;

/**
 * 应用初始化钩子，处理请求IP黑名单
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class BlackList
{
    /**
     * 钩子回调方法
     *
     * @return void
     */
    public function handler()
    {
        $blackList = Config::instance()->get('blacklist', []);
        $ip = Request::instance()->ip();
        if (!empty($blackList) && !empty($ip)) {
            if (Tool::instance()->safe_ip($ip, $blackList)) {
                return Url::instance()->abort(404);
            }
        }

        return true;
    }
}
