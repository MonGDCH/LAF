<?php

namespace Laf;

use Exception;

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
     * 回调服务
     *
     * @param string $name      方法名
     * @param mixed $arguments 参数列表
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (is_null($this->service)) {
            throw new Exception('Kernel Service is NULL!');
        }
        return call_user_func_array([$this->service, $name], (array) $arguments);
    }
}
