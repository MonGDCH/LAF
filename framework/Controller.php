<?php
namespace Laf;

use mon\factory\Container;

/**
 * 控制器基类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Controller
{
    /**
     * 服务容器
     *
     * @var [type]
     */
    protected $container;

    /**
     * 请求实例
     *
     * @var [type]
     */
    protected $request;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->container = Container::instance();
        $this->request = $this->container->make('request');
    }
}
