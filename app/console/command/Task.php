<?php

namespace app\console\command;

use Laf\Log;
use mon\env\Config;
use Channel\Client;
use Workerman\Worker;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;
use app\libs\ChannelServer;

/**
 * 基于Channel异步task服务
 * 
 * @requires workerman/channel
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Task extends Command
{
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

        // 获取异步通信配置
        $config = Config::instance()->get('task', []);
        if (!$config) {
            return $out->write('get task config faild!');
        }
        // 创建异步通信服务
        $channelServer = new ChannelServer($config['server_ip'], $config['port']);
        // 启动监听
        $channelServer->getWorker()->onWorkerStart = function ($worker) use ($config) {
            // 命令控制台启动脚本，Mysql链接断开自动重连
            Log::instance()->setConfig(['logPath' => RUNTIME_PATH . '/log/task']);
            Log::instance()->info('task service start')->save();

            // 链接通信服务
            Client::connect($config['client_ip'], $config['port']);
            // 监听事件
            foreach ($config['cmd'] as $cmd => $item) {
                $callback = new $item['callback'];
                switch ($item['type']) {
                    case 'event':
                        Client::on($cmd, [$callback, 'handle']);
                        break;
                    case 'queue':
                        Client::watch($cmd, [$callback, 'handle']);
                        break;
                }
                unset($callback);
            }
        };

        Worker::runAll();
        return 0;
    }
}
