<?php

namespace Laf\provider;

use Laf\Provider;
use mon\factory\Container;
use mon\store\Cookie as Service;

/**
 * Cookie组件封装
 */
class Cookie extends Provider
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        $config = Container::instance()->config->get('cookie', []);
        $this->service = new Service($config);
    }
}
