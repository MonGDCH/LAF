<?php

namespace Laf;

/**
 * 服务提供抽象类
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
abstract class Provider
{
    /**
     * 服务实例
     *
     * @var mixed
     */
    protected $service;

    /**
     * 获取服务
     *
     * @return mixed
     */
    abstract public function getService();

    /**
     * 回调服务
     *
     * @param string $name      方法名
     * @param mixed $arguments 参数列表
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getService(), $name], (array) $arguments);
    }
}
