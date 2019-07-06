<?php

namespace Laf\provider;

use Laf\Kernel;
use mon\factory\Container;
use mon\store\Rdb as Service;

/**
 * Redis组件封装
 */
class Redis extends Kernel
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        $config = Container::instance()->config->get('redis', []);
        $this->service = new Service($config);
    }
}
