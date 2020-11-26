<?php

/*
|--------------------------------------------------------------------------
| 获取应用实例
|--------------------------------------------------------------------------
| 这里获取应用实例
|
*/
$app = require __DIR__ . '/bootstrap.php';

/*
|--------------------------------------------------------------------------
| 注册框架应用钩子
|--------------------------------------------------------------------------
| 这里搭载框架应用钩子
|
*/
$app->definition($tags['app']);

/*
|--------------------------------------------------------------------------
| 初始化应用
|--------------------------------------------------------------------------
| 这里初始化应用, 定义是否为开发环境
|
*/
$app->init((strtolower(RUN_MODE) !== 'prd'));

return $app;
