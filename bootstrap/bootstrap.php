<?php

/*
|--------------------------------------------------------------------------
| 定义应用运行模式
|--------------------------------------------------------------------------
| 这里定义应用运行模式, 分别为:
| dev: 开发环境, 打开错误提示
| sim: 预发布环境, 打开错误提示
| prd: 生产环境, 关闭错误提示
|
*/
define('RUN_MODE', 'dev');

/*
|--------------------------------------------------------------------------
| 定义应用根路径
|--------------------------------------------------------------------------
| 这里定义根路径, 用于路径查找
|
*/
define('ROOT_PATH', dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| 定义应用APP路径
|--------------------------------------------------------------------------
| 这里定义APP路径, 用于路径查找
|
*/
define('APP_PATH', ROOT_PATH . '/app');

/*
|--------------------------------------------------------------------------
| 定义配置文件路径
|--------------------------------------------------------------------------
| 这里定义配置文件路径, 用于路径查找获取配置信息
|
*/
define('CONFIG_PATH', __DIR__ . '/config');

/*
|--------------------------------------------------------------------------
| 静态资源路径
|--------------------------------------------------------------------------
| 这里定义静态资源路径, 需拥有读写权限，插件安装时如有资源依赖，会同步文件到对应的路径下
|
*/
define('STATIC_PATH', ROOT_PATH . '/public/static');

/*
|--------------------------------------------------------------------------
| 定义路由缓存文件路径
|--------------------------------------------------------------------------
| 这里定义路由缓存文件路径, 存在路由缓存文件则不重新加载路由定义文件
|
*/
define('ROUTE_CACHE', ROOT_PATH . '/storage/cache/router.php');

/*
|--------------------------------------------------------------------------
| 加载composer
|--------------------------------------------------------------------------
| 加载composer, 处理类文件自动加载
|
*/
require_once ROOT_PATH . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| 创建应用实例
|--------------------------------------------------------------------------
| 这里创建应用实例
|
*/
$app = \FApi\App::instance();

/*
|--------------------------------------------------------------------------
| 获取配置服务
|--------------------------------------------------------------------------
| 这里获取配置服务, 定义配置环境
|
*/
$config = \mon\env\Config::instance();

/*
|--------------------------------------------------------------------------
| 注册配置信息
|--------------------------------------------------------------------------
| 这里注册加载全局配置信息
|
*/
$config->register(require(CONFIG_PATH . '/config.php'));

/*
|--------------------------------------------------------------------------
| 获取钩子信息
|--------------------------------------------------------------------------
| 这里加载全局使用的钩子配置信息
|
*/
$tags = require(CONFIG_PATH . '/tags.php');

/*
|--------------------------------------------------------------------------
| 定义应用时区
|--------------------------------------------------------------------------
| 这里设置时区
|
*/
date_default_timezone_set($config->get('time_zone', 'PRC'));


/*
|--------------------------------------------------------------------------
| 注册服务
|--------------------------------------------------------------------------
| 这里注册定义全局服务
|
*/
$app->singleton(require(CONFIG_PATH . '/provider.php'));


/*
|--------------------------------------------------------------------------
| 配置数据库
|--------------------------------------------------------------------------
| 这里配置数据库链接信息
|
*/
\mon\orm\Db::setConfig($config->get('database', []));


/*
|--------------------------------------------------------------------------
| 定义数据库事件钩子
|--------------------------------------------------------------------------
| 这里配置数据库执行SQL事件的钩子
|
*/
foreach ((array) $tags['db'] as $event => $callback) {
    if (!empty($callback)) {
        if (is_string($callback)) {
            \mon\orm\Db::event($event, $callback);
        } else {
            throw new Exception('[ERROR] DB event unsupported hook type! event: ' . $event, 500);
        }
    }
}


/*
|--------------------------------------------------------------------------
| 定义路由
|--------------------------------------------------------------------------
| 注册应用请求路由
|
*/
if (file_exists(ROUTE_CACHE)) {
    $app->route->setData(require(ROUTE_CACHE));
} else {
    $app->route->group([], function ($router) {
        require_once ROOT_PATH . '/app/http/router.php';
    });
}

return $app;
