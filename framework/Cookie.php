<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\store\Cookie as Service;

/**
 * Cookie组件封装
 * 
 * @method void set(string $key, mixed $value = '', array $option = [])  设置cookie
 * @method void forever(string $name, mixed $value = '', array $option = [])  永久保存
 * @method boolean has(string $name, string $prefix = null)  判断是否存在
 * @method mixed get(string $key = '', mixed $default = null, string $prefix = null)  获取cookie
 * @method void del(string $key, string $prefix = null)  删除cookie
 * @method void clear(string $prefix = null)  清空cookie
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.1 优化注解 2022-07-15
 */
class Cookie extends Provider
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
            $config = Config::instance()->get('app.cookie', []);
            $this->service = new Service($config);
        }

        return $this->service;
    }
}
