<?php

namespace app\console\socket;

use mon\util\Instance;
use GatewayWorker\Lib\Gateway;

/**
 * 测试Socket服务指令
 */
class TestSocket
{
    use Instance;

    /**
     * 执行指令回调业务
     *
     * @param array $query 请求参数
     * @return void
     */
    public function handle($query, $client_id)
    {
        if (!isset($query['type'])) {
            return Gateway::sendToCurrentClient($this->result(406, 'cmd unrecognized'));
        }
        switch ($query['type']) {
            case 'ping':
                // 客户端心跳
                Gateway::sendToCurrentClient($this->result(200, 'pong'));
                break;
            default:
                Gateway::sendToCurrentClient($this->result(404, 'type not found'));
        }
    }

    /**
     * 返回结果集
     *
     * @param integer $code
     * @param string $message
     * @param array $data
     * @param boolean $toJson
     * @return string
     */
    public function result($code, $message, $data = [], $toJson = true)
    {
        $data = [
            'code'  => $code,
            'msg'   => $message,
            'data'  => $data
        ];
        return $toJson ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
    }
}
