<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\auth\rbac\Auth;

/**
 * RBAC权限控制服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Rbac extends Provider
{
    use Instance;

    /**
     * 获取服务
     *
     * @return Auth
     */
    public function getService()
    {
        if (is_null($this->service)) {
            $config = Config::instance()->get('app.rbac', []);
            $config['database'] = Config::instance()->get('database', []);
            $this->service = Auth::instance()->init($config);
        }

        return $this->service;
    }
}
