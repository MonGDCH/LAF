#!/usr/bin/env php
<?php

/*
|--------------------------------------------------------------------------
| Linux 启动应用
|--------------------------------------------------------------------------
| Linux服务器环境下启用服务应用脚本
*/
ini_set('display_errors', 'on');

// 屏蔽win环境
if (strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows, please use start_service.bat\n");
}

// 检查扩展
if (!extension_loaded('pcntl')) {
    exit("Please install pcntl extension. See http://doc.workerman.net/appendices/install-extension.html\n");
}

if (!extension_loaded('posix')) {
    exit("Please install posix extension. See http://doc.workerman.net/appendices/install-extension.html\n");
}

// 定义常量
define('GLOBAL_START', 1);

/**
 * 获取控制台应用实例
 */
$console = require_once __DIR__ . '/bootstrap/bootstrap.php';

// 加载所有service/main/*.php，以便启动所有服务
foreach (glob(__DIR__ . '/service/main/*.php') as $file) {
    require_once $file;
}
// 运行所有服务
\Workerman\Worker::runAll();
