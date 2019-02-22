<?php
namespace App\Libs;

/**
 * OAuth工厂
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class OAuth
{
    /**
     * oauth实例控制
     *
     * @var array
     */
    public static $oauthInstance = [];

    /**
     * 配置
     *
     * @var [type]
     */
    public static $config = [
        'api'   => [
            'salt'      => 'api_91pizza_20182019',
            // token key值
            'token_key' => 'mon_fapi_key_pizza',
        ]
    ];

    /**
     * 获取实例
     *
     * @return [type] [description]
     */
    public static function get($name)
    {
        if(!isset(self::$oauthInstance[$name]))
        {
            $configObj = self::$config[$name];
            // yaf配置对象转数组
            $config = [];
            foreach($configObj as $k => $v){
                $config[$k] = $v;
            }

            self::$oauthInstance[$name] = new \Laf\lib\OAuth($config);
        }
        
        return self::$oauthInstance[$name];
    }

    /**
     * 注册oauth实例
     *
     * @param  [type] $name     实例名
     * @param  [type] $instance 实例
     * @return [type]           [description]
     */
    public static function register($name, $instance)
    {
        self::$oauthInstance[$name] = $instance;
    }

    /**
     * 删除oauth实例
     *
     * @param  [type] $name 实例名
     * @return [type]       [description]
     */
    public static function remove($name)
    {
        unset(static::$oauthInstance[$name]);
    }
}