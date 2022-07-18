<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\store\Session as Service;

/**
 * Session组件封装
 * 
 * @method void set(string $key, mixed $value = '', array $option = [])  设置session
 * @method boolean has(string $name, string $prefix = null)  判断是否存在
 * @method mixed get(string $key = '', mixed $default = null, string $prefix = null)  获取session
 * @method void del(string $key, string $prefix = null)  删除session
 * @method void clear(string $prefix = null)  清空session
 * @method string getSessionId()  获取session_id
 * @method void regenerate($delete = false)  重新生成session_id
 * 
 * @author Mon <095558837@qq.com>
 * @version 1.0.1 优化注解  2022-07-15
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
