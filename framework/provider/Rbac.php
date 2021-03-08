<?php

namespace Laf\provider;

use Laf\Provider;
use mon\util\Container;
use mon\auth\rbac\Auth;

/**
 * RBAC权限控制服务
 */
class Rbac extends Provider
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        $config = Container::instance()->config->get('rbac', []);
        $config['database'] = Container::instance()->config->get('database');
        $this->service = Auth::instance()->init($config);
    }
}
