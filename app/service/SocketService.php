<?php

namespace app\service;

use mon\util\Instance;
use GatewayWorker\Lib\Gateway;

/**
 * socket通信服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class SocketService
{
    use Instance;

    /**
     * socket通信业务处理 
     *
     * @param string $client_id 客服端链接ID
     * @param string $message 通信讯息
     * @return void
     */
    public function handle($client_id, $message)
    {
        Gateway::sendToCurrentClient('hello：' . $message);
    }
}
