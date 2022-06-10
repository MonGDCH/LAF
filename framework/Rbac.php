<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\auth\rbac\Auth;

/**
 * RBAC权限控制服务
 * 
 * @method boolean check(string|array $name, integer $uid, boolean $relation = true) 校验权限
 * @method mixed model(string $name, boolean $cache = true) 获取权限模型
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
            $config['database'] = Config::instance()->get('database.default', []);
            $this->service = Auth::instance()->init($config);
        }

        return $this->service;
    }
}
