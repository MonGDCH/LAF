<?php

namespace Laf\provider;

use Laf\Provider;
use mon\env\Config;
use mon\util\Instance;
use mon\store\Cache as Service;

/**
 * Cache组件封装
 * 
 * @method \mon\store\cache\Driver get(string $name, mixed $default = null) mixed 获取缓存
 * @method \mon\store\cache\Driver set(string $name, mixed $value, integer $expire = null) boolean 设置缓存
 * @method \mon\store\cache\Driver inc(string $name, integer $step = 1) boolean 缓存自增
 * @method \mon\store\cache\Driver dec(string $name, integer $step = 1) boolean 缓存自减
 * @method \mon\store\cache\Driver has(string $name) boolean 是否存在缓存
 * @method \mon\store\cache\Driver remove(string $name) boolean 删除缓存
 * @method \mon\store\cache\Driver clear() boolean 清空缓存
 * @method \mon\store\cache\Driver pull(string $name) mixed 读取缓存并删除
 * @method \mon\store\cache\Driver handler() mixed 获取驱动
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 * @return mixed
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
