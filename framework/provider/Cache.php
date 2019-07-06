<?php

namespace Laf\provider;

use Laf\Kernel;
use mon\factory\Container;
use mon\store\Cache as Service;

/**
 * Cache组件封装
 */
class Cache extends Kernel
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
