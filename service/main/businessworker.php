<?php
/*
|--------------------------------------------------------------------------
| bussinessWorker进程
|--------------------------------------------------------------------------
| bussinessWorker进程配置
*/
require_once __DIR__ . '/../../bootstrap/bootstrap.php';

use mon\env\Config;

// 定义worker日志名称
$workerLog = Config::instance()->get('log.logPath');;
\Workerman\Worker::$logFile = $workerLog ? ($workerLog . '/workerman.log') : '';
// 创建实例
$business = new \GatewayWorker\BusinessWorker();
// worker名称
$business->name = Config::instance()->get('service.business.name');
// bussinessWorker进程数量
$business->count = Config::instance()->get('service.business.count');
// 服务注册地址
$business->registerAddress = Config::instance()->get('service.business.registerAddress');
// 服务回调对象，含命名空间
$business->eventHandler = Config::instance()->get('service.business.eventHandler');

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    \Workerman\Worker::runAll();
}
