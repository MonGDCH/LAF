<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\store\Cookie as Service;

/**
 * Cookie组件封装
 * 
 * @method Service set($key, $value = '', array $option = []) void 设置cookie
 * @method Service forever($name, $value = '', array $option = []) void 永久保存
 * @method Service has($name, $prefix = null) boolean 判断是否存在
 * @method Service get($key = '', $default = null, $prefix = null) mixed 获取cookie
 * @method Service del($key, $prefix = null) void 删除cookie
 * @method Service clear($prefix = null) void 清空cookie
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
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