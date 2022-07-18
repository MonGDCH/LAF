<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\auth\rbac\Auth;

/**
 * RBAC权限控制服务
 * 
 * @method boolean check(string|array $name, integer $uid, boolean $relation = true) 校验权限
 * @method array getAuthIds(integer $uid) 获取角色权限节点对应权限
 * @method array getAuthList(integer $uid) 获取用户权限规则列表
 * @method array getRule(integer $uid) 获取权限规则
 * @method mixed model(string $name, boolean $cache = true) 获取权限模型
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.1 优化注解  2022-07-15
 */
class Rbac extends Provider
{
    use Instance;

    /**
     * 获取服务
     *
     * @return Auth
     */
    public function getService()
    {
        if (is_null($this->service)) {
            $config = Config::instance()->get('app.rbac', []);
            $config['database'] = Config::instance()->get('database.default', []);
            $this->service = Auth::instance()->init($config);
        }

        return $this->service;
    }
}
