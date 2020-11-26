<?php
/*
|--------------------------------------------------------------------------
| gateway进程
|--------------------------------------------------------------------------
| gateway进程配置
*/
require_once __DIR__ . '/../../bootstrap/bootstrap.php';

use mon\env\Config;

// 定义worker日志名称
$workerLog = Config::instance()->get('log.logPath');
\Workerman\Worker::$logFile = $workerLog ? ($workerLog . '/workerman.log') : '';
// 创建实例
$gateway = new \GatewayWorker\Gateway(Config::instance()->get('service.gateway.address'), Config::instance()->get('service.gateway.context'));
// 传输协议
$gateway->transport = Config::instance()->get('service.gateway.transport', 'tcp');
// gateway名称，status方便查看
$gateway->name = Config::instance()->get('service.gateway.name');
// gateway进程数，gateway进程数建议与cpu核数相同
$gateway->count = Config::instance()->get('service.gateway.count');
// 本机ip，分布式部署时使用内网ip
$gateway->lanIp = Config::instance()->get('service.gateway.lanIp');
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口 
$gateway->startPort = Config::instance()->get('service.gateway.startPort');
// 心跳间隔
$gateway->pingInterval = Config::instance()->get('service.gateway.pingInterval');
// 心跳数据
$gateway->pingData = Config::instance()->get('service.gateway.pingData');
// （pingInterval * pingNotResponseLimit = 间隔时间） 秒内没有任何请求则服务端认为对应客户端已经掉线，服务端关闭连接并触发onClose回调。
$gateway->pingNotResponseLimit = Config::instance()->get('service.gateway.pingNotLimit');
// 服务注册地址
$gateway->registerAddress = Config::instance()->get('service.gateway.registerAddress');

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    \Workerman\Worker::runAll();
}
