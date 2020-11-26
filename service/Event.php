<?php

namespace service;

use app\service\Orther;
use service\Result;
use mon\env\Config;
use Laf\provider\Log;
use GatewayWorker\Lib\Gateway;

/**
 * 事件回调入口服务
 */
class Event
{
    /**
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次。
     *
     * @param \GatewayWorker\BusinessWorker $businessWorker
     * @return void
     */
    public static function onWorkerStart($businessWorker)
    {
        // 命令控制台启动脚本，Mysql链接断开自动重连
        \mon\orm\Db::setConfig(['break_reconnect' => true]);

        Log::instance()->info('WorkerStart')->save();
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param integer $client_id 连接id
     * @return void
     */
    public static function onConnect($client_id)
    {
        Log::instance()->info('clinet connect, IP: ' . $_SERVER['REMOTE_ADDR']);
        // 处理白名单
        if (Config::instance()->get('service.whiteList') && !in_array($_SERVER['REMOTE_ADDR'], (array)Config::instance()->get('service.whiteList'))) {
            // 不存在白名单列表中，直接断开连接
            Gateway::closeClient($client_id);
            Log::instance()->info('clinet not in white list, close connect, IP: ' . $_SERVER['REMOTE_ADDR'] . ', clientID: ' . $client_id)->save();
            return;
        }

        // 记录客户端ID
        $_SESSION['clientID'] = $client_id;
        // 通知客户端ID
        Gateway::sendToCurrentClient(Result::instance()->data(Result::CONNECT, ['id' => $client_id]));
        Log::instance()->save();
    }

    /**
     * 当客户端连接上gateway完成websocket握手时触发的回调函数。
     * 注意：此回调只有gateway为websocket协议并且gateway没有设置onWebSocketConnect时才有效。
     *
     * @param integer $client_id 客户端ID
     * @param mixed $data 请求的GET数据及$_SERVER数据(含HTTP相关数据)
     * @return void
     */
    public static function onWebSocketConnect($client_id, $data)
    {
        // 处理websocket允许连接的域名
        if (Config::instance()->get('service.websocket_domain') && !in_array($data['server']['HTTP_ORIGIN'], (array)Config::instance()->get('service.websocket_domain'))) {
            // 不存在白名单列表中，直接断开连接
            Gateway::closeClient($client_id);
            Log::instance()->info('clinet not in websocket domain, close connect, IP: ' . $_SERVER['REMOTE_ADDR'] . ', domain: ' . $data['server']['HTTP_ORIGIN'])->save();
            return;
        }
    }

    /**
     * 当客户端发来消息时触发
     *
     * @param integer $client_id 连接id
     * @param mixed $message 具体消息
     * @return void
     */
    public static function onMessage($client_id, $message)
    {
        Log::instance()->info('clinet send message, IP: ' . $_SERVER['REMOTE_ADDR'] . ', message: ' . $message);
        //  业务操作
        $query = json_decode($message, true);
        if (!isset($query['cmd'])) {
            // 不存在指令, 返回错误提示
            Gateway::sendToCurrentClient(Result::instance()->data(Result::ERR_CMD_NOTFOUND));
        } else {
            // 获取指定业务cmd指令
            $cmd = ucfirst($query['cmd']);
            $class = '\app\service\\' . $cmd;
            if (!class_exists($class)) {
                // 存在未知指令，调用未知指令handle方法
                Orther::instance()->handle($query);
            } else {
                // 执行指定指令handle方法
                $class::instance()->handle($query);
            }
        }

        Log::instance()->save();
    }

    /**
     * 当用户断开连接时触发
     *
     * @param integer $client_id 连接id
     * @return void
     */
    public static function onClose($client_id)
    {
        Log::instance()->info('clinet close connect, IP: ' . $_SERVER['REMOTE_ADDR'] . ', clientID: ' . $client_id)->save();
    }

    /**
     * 当businessWorker进程退出时触发。每个进程生命周期内都只会触发一次。
     * 注意：某些情况将不会触发onWorkerStop，例如业务出现致命错误FatalError，或者进程被强行杀死等情况。
     *
     * @param \GatewayWorker\BusinessWorker $businessWorker
     * @return void
     */
    public static function onWorkerStop($businessWorker)
    {
        Log::instance()->info('WorkerStop')->save();
    }
}
