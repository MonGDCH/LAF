<?php

namespace app\console\command;

use Channel\Client;
use Workerman\Worker;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;
use app\console\libs\ChannelServer;

/**
 * 异步通信服务
 * 
 * @requires workerman/channel
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Channel extends Command
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
        $ip = '127.0.0.1';
        $port = 2206;
        // 创建异步通信服务
        $channelServer = new ChannelServer($ip, $port);
        // 启动监听
        $channelServer->getWorker()->onWorkerStart = function ($worker) use ($ip, $port) {
            // 链接通信服务
            Client::connect($ip, $port);
            // 监听test事件
            Client::on('test', [$this, 'test']);
        };

        Worker::runAll();
        return 0;
    }

    /**
     * Test事件回调
     *
     * @param mixed $data publish数据
     * @return void
     */
    public function test($data)
    {
        debug($data);
    }
}
