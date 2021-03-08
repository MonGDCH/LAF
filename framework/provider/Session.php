<?php

namespace Laf\provider;

use Laf\Provider;
use mon\util\Container;
use mon\store\Session as Service;

/**
 * Session组件封装
 */
class Session extends Provider
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        $config = Container::instance()->config->get('session', []);
        $this->service = new Service($config);
    }
}
