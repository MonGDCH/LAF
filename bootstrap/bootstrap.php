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
define('ROOT_PATH', __DIR__ . '/..');

/*
|--------------------------------------------------------------------------
| 加载composer
|--------------------------------------------------------------------------
| 加载composer, 处理类文件自动加载
|
*/
require ROOT_PATH . '/vendor/autoload.php';


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
| 注册配置信息
|--------------------------------------------------------------------------
| 这里注册加载全局配置信息
|
*/
$app->register( require(__DIR__.'/config/config.php') );


/*
|--------------------------------------------------------------------------
| 定义应用时区
|--------------------------------------------------------------------------
| 这里设置时区
|
*/
date_default_timezone_set( $app->config->get('time_zone', 'PRC') );


/*
|--------------------------------------------------------------------------
| 注册服务
|--------------------------------------------------------------------------
| 这里注册定义全局服务
|
*/
$app->singleton( require(__DIR__.'/config/kernel.php') );


/*
|--------------------------------------------------------------------------
| 配置数据库
|--------------------------------------------------------------------------
| 这里配置数据库链接信息
|
*/
\mon\Db::setConfig( require(__DIR__.'/config/database.php') );

/*
|--------------------------------------------------------------------------
| 定义路由
|--------------------------------------------------------------------------
| 注册应用请求路由
|
*/
$app->route->group([
    'namespace' => 'App\Http\Controller\\',
], function ($router) {
    require __DIR__.'/../app/Http/router.php';
});

return $app;