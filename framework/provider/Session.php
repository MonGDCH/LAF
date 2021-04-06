<?php

namespace Laf\provider;

use Laf\Provider;
use mon\env\Config;
use mon\util\Instance;
use mon\store\Session as Service;

/**
 * Session组件封装
 * 
 * @method Service set($key, $value = '', array $option = []) void 设置session
 * @method Service has($name, $prefix = null) boolean 判断是否存在
 * @method Service get($key = '', $default = null, $prefix = null) mixed 获取session
 * @method Service del($key, $prefix = null) void 删除session
 * @method Service clear($prefix = null) void 清空session
 * 
 * @author Mon <095558837@qq.com>
 * @version 1.0.0
 */
class Session extends Provider
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
            $config = Config::instance()->get('app.session', []);
            $this->service = new Service($config);
        }

        return $this->service;
    }
}
