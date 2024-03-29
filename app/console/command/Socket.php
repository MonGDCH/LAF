<?php

namespace app\console\command;

use Laf\Log;
use mon\util\Tool;
use mon\env\Config;
use Workerman\Worker;
use mon\console\Input;
use mon\console\Output;
use mon\util\Container;
use mon\console\Command;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Lib\Gateway as LibGateway;

/**
 * 基于GateWay的socket服务
 * 
 * @requires workerman/gateway-worker
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Socket extends Command
{
    /**
     * 链接成功
     * 
     * @var integer
     */
    const CONNECT = 200;

    /**
     * 不存在对应指令映射
     * 
     * @var integer
     */
    const NOT_FOUND = 404;

    /**
     * 鉴权未通过
     * 
     * @var integer
     */
    const NOT_AUTH = 403;

    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer exit状态码
     */
    public function execute($in, $out)
    {
        // 定义worker日志名称
        $workerLog = Config::instance()->get('app.log.logPath');
        Worker::$logFile = $workerLog ? ($workerLog . '/workerman.log') : '';
        // 判断是否为主业务register
        if (Config::instance()->get('socket.register.init')) {
            // 创建register实例
            $register = new Register(Config::instance()->get('socket.register.address'));
            // register名称，status方便查看
            $register->name = Config::instance()->get('socket.register.name');
        }

        // 创建businessWorker实例
        $business = new BusinessWorker();
        // businessWorker名称
        $business->name = Config::instance()->get('socket.business.name');
        // bussinessWorker进程数量
        $business->count = Config::instance()->get('socket.business.count');
        // 服务注册地址
        $business->registerAddress = Config::instance()->get('socket.business.registerAddress');
        // 服务回调对象，含命名空间
        $business->eventHandler = Socket::class;

        // 创建Gateway实例
        $gateway = new Gateway(Config::instance()->get('socket.gateway.address'), Config::instance()->get('socket.gateway.context'));
        // 传输协议
        $gateway->transport = Config::instance()->get('socket.gateway.transport', 'tcp');
        // gateway名称，status方便查看
        $gateway->name = Config::instance()->get('socket.gateway.name');
        // gateway进程数，gateway进程数建议与cpu核数相同
        $gateway->count = Config::instance()->get('socket.gateway.count');
        // 本机ip，分布式部署时使用内网ip
        $gateway->lanIp = Config::instance()->get('socket.gateway.lanIp');
        // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
        // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口 
        $gateway->startPort = Config::instance()->get('socket.gateway.startPort');
        // 心跳间隔
        $gateway->pingInterval = Config::instance()->get('socket.gateway.pingInterval');
        // 心跳数据
        $gateway->pingData = Config::instance()->get('socket.gateway.pingData');
        // （pingInterval * pingNotResponseLimit = 间隔时间） 秒内没有任何请求则服务端认为对应客户端已经掉线，服务端关闭连接并触发onClose回调。
        $gateway->pingNotResponseLimit = Config::instance()->get('socket.gateway.pingNotLimit');
        // 服务注册地址
        $gateway->registerAddress = Config::instance()->get('socket.gateway.registerAddress');

        // 运行workermane
        Worker::runAll();
        return 0;
    }

    /**
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次。
     *
     * @param \GatewayWorker\BusinessWorker $businessWorker
     * @return void
     */
    public static function onWorkerStart($businessWorker)
    {
        // 命令控制台启动脚本，Mysql链接断开自动重连
        Log::instance()->setConfig(['logPath' => RUNTIME_PATH . '/log/socket']);
        Log::instance()->info('socket service start')->save();
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
        if (Config::instance()->get('socket.whiteList') && !in_array($_SERVER['REMOTE_ADDR'], (array)Config::instance()->get('socket.whiteList'))) {
            // 不存在白名单列表中，直接断开连接
            LibGateway::closeClient($client_id);
            Log::instance()->info('clinet not in white list, close connect, IP: ' . $_SERVER['REMOTE_ADDR'] . ', clientID: ' . $client_id)->save();
            return;
        }
        // 通知客户端ID
        LibGateway::sendToCurrentClient(json_encode(['code' => self::CONNECT, 'msg' => 'connect success', 'data' => ['id' => $client_id]], JSON_UNESCAPED_UNICODE));
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
        if (Config::instance()->get('socket.websocket_domain') && !in_array($data['server']['HTTP_ORIGIN'], (array)Config::instance()->get('socket.websocket_domain'))) {
            // 不存在白名单列表中，直接断开连接
            LibGateway::closeClient($client_id);
            Log::instance()->info('clinet not in websocket domain, close connect, IP: ' . $_SERVER['REMOTE_ADDR'] . ', domain: ' . $data['server']['HTTP_ORIGIN'])->save();
            return;
        }
        // 链接鉴权
        $safeConfig = Config::instance()->get('socket.safe');
        if ($safeConfig['auth']) {
            // 客服系统，使用另外的统一的秘钥
            $salt = $safeConfig['salt'];
            $token = isset($data['cookie'][$safeConfig['token']]) ? $data['cookie'][$safeConfig['token']] : '';
            $time = isset($data['cookie'][$safeConfig['time']]) ? $data['cookie'][$safeConfig['time']] : '';
            $ticket = isset($data['cookie'][$safeConfig['name']]) ? $data['cookie'][$safeConfig['name']] : '';
            $check = Tool::instance()->checkTicket($ticket, $token, $time, $salt, false, $safeConfig['expire']);
            if (!$check) {
                // 未通过鉴权，断开链接
                LibGateway::sendToCurrentClient(json_encode(['code' => self::NOT_AUTH, 'msg' => 'no auth permission', 'data' => []], JSON_UNESCAPED_UNICODE));
                LibGateway::closeClient($client_id);
            }
            // 保存已通过鉴权的ticket
            $_SESSION['ticket'] = $ticket;
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
        // 业务操作
        $query = json_decode($message, true);
        $cmd = Config::instance()->get('socket.cmd', []);
        if (!isset($query['cmd']) || !isset($cmd[$query['cmd']])) {
            // 不存在指令, 返回错误提示
            LibGateway::sendToCurrentClient(json_encode([
                'code'  => Socket::NOT_FOUND,
                'msg'   => 'cmd not found'
            ], JSON_UNESCAPED_UNICODE));
        } else {
            // 执行指令回调
            $callback = $cmd[$query['cmd']];
            Container::instance()->invokeMethd([$callback, 'handle'], [$query, $client_id]);
        }

        // 写入日志
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
        // 退出链接, 访客退出，通知客服
        // if (isset($_SESSION['role']) && $_SESSION['role'] == 'visitor' && !count(LibGateway::getClientIdByUid($_SESSION['uid']))) {
        //     // 移除会话
        //     LibGateway::leaveGroup($client_id, "chat_" . $_SESSION['app']);
        //     // 通知客服
        //     LibGateway::sendToUid($_SESSION['group'], json_encode([
        //         'code'      => 200,
        //         // 访客退出标志
        //         'type'      => 'visitorExit',
        //         // 提示信息
        //         'msg'       => '客户断开连接...',
        //         'data'      => [
        //             'type'  => 'visitorExit',
        //             // 访客的标识uid
        //             'client'    => $_SESSION['uid'],
        //         ]
        //     ], JSON_UNESCAPED_UNICODE));
        // }

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
        Log::instance()->info('socket service stop')->save();
    }
}
