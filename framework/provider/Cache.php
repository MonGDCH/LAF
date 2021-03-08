<?php

namespace Laf\provider;

use Laf\Provider;
use mon\util\Container;
use mon\store\Cache as Service;

/**
 * Cache组件封装
 */
class Cache extends Provider
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        $config = Container::instance()->config->get('cache', []);
        $this->service = new Service($config);
    }
}
