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
define('CONFIG_PATH', ROOT_PATH . '/config');


/*
|--------------------------------------------------------------------------
| 运行时目录路径
|--------------------------------------------------------------------------
| 这里定义运行时路径, 用于存储系统运行时相关资源内容，目录需拥有读写权限
|
*/
define('RUNTIME_PATH', ROOT_PATH . '/runtime');


/*
|--------------------------------------------------------------------------
| 定义路由缓存文件路径
|--------------------------------------------------------------------------
| 这里定义路由缓存文件路径, 存在路由缓存文件则不重新加载路由定义文件
|
*/
define('ROUTE_CACHE', RUNTIME_PATH . '/cache/router.php');


/*
|--------------------------------------------------------------------------
| 定义配置缓存文件路径
|--------------------------------------------------------------------------
| 这里定义配置缓存文件路径, 存在配置缓存文件则不重新加载配置定义文件
|
*/
define('CONFIG_CACHE', RUNTIME_PATH . '/cache/config.php');


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
if (PHP_SAPI != 'cli' && PHP_SAPI != 'cli-server' && file_exists(CONFIG_CACHE)) {
    $config->register(require(CONFIG_CACHE));
} else {
    $configFiles = [];
    if (is_dir(CONFIG_PATH)) {
        $configFiles = glob(CONFIG_PATH . '/*.php');
    }
    foreach ($configFiles as $file) {
        $config->load($file, pathinfo($file, PATHINFO_FILENAME));
    }
}


/*
|--------------------------------------------------------------------------
| 定义应用时区
|--------------------------------------------------------------------------
| 这里设置时区
|
*/
date_default_timezone_set($config->get('app.time_zone', 'PRC'));


/*
|--------------------------------------------------------------------------
| 注册服务
|--------------------------------------------------------------------------
| 这里注册定义全局服务
|
*/
$app->singleton($config->get('provider', []));


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
foreach ($config->get('tags.database', []) as $event => $listen) {
    foreach ($listen as $callback) {
        \mon\orm\Db::listen($event, $callback);
    }
}


/*
|--------------------------------------------------------------------------
| 定义路由
|--------------------------------------------------------------------------
| 注册应用请求路由
|
*/
if (PHP_SAPI != 'cli' && PHP_SAPI != 'cli-server' && file_exists(ROUTE_CACHE)) {
    $app->route->setData(require(ROUTE_CACHE));
} else {
    $app->route->group([], function ($router) {
        require_once APP_PATH . '/http/router.php';
    });
}

return $app;
