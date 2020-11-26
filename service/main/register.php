<?php
/*
|--------------------------------------------------------------------------
| Register服务
|--------------------------------------------------------------------------
| register 必须是text协议
*/
require_once __DIR__ . '/../../bootstrap/bootstrap.php';

use mon\env\Config;

// 定义worker日志名称
$workerLog = Config::instance()->get('log.logPath');;
\Workerman\Worker::$logFile = $workerLog ? ($workerLog . '/workerman.log') : '';
// 判断是否为主业务register
if (Config::instance()->get('service.register.init')) {
    // 创建实例
    $register = new \GatewayWorker\Register(Config::instance()->get('service.register.address'));
    // register名称，status方便查看
    $register->name = Config::instance()->get('service.register.name');

    // 如果不是在根目录启动，则运行runAll方法
    if (!defined('GLOBAL_START')) {
        \Workerman\Worker::runAll();
    }
}
