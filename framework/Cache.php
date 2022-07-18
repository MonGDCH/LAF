<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\store\Cache as Service;

/**
 * Cache组件封装
 * 
 * @method mixed get(string $name, mixed $default = null)  获取缓存
 * @method boolean set(string $name, mixed $value, integer $expire = null)  设置缓存
 * @method boolean inc(string $name, integer $step = 1)  缓存自增
 * @method boolean dec(string $name, integer $step = 1)  缓存自减
 * @method boolean has(string $name)  是否存在缓存
 * @method boolean remove(string $name)  删除缓存
 * @method boolean clear()  清空缓存
 * @method mixed pull(string $name)  读取缓存并删除
 * @method mixed handler()  获取驱动
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.1 优化注解  2022-07-15
 */
class Cache extends Provider
{
    use Instance;

    /**
     * 获取服务
     *
     * @return Service
     */
    public function getService()
    {
        if (is_null($this->service)) {
            $config = Config::instance()->get('app.cache', []);
            $this->service = new Service($config);
        }

        return $this->service;
    }
}
