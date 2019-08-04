<?php
namespace Laf;

use Exception;

class Provider
{
    /**
     * 服务实例
     *
     * @var [type]
     */
    protected $service;

    /**
     * 回调服务
     *
     * @param [type] $name      方法名
     * @param [type] $arguments 参数列表
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (is_null($this->service)) {
            throw new Exception('Kernel Service is NULL!');
        }
        return call_user_func_array([$this->service, $name], (array)$arguments);
    }
}
