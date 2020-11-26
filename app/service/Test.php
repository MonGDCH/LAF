<?php

namespace app\service;

use service\Result;
use service\Service;
use mon\util\Instance;
use GatewayWorker\Lib\Gateway;

/**
 * 测试指令处理器
 */
class Test implements Service
{
    use Instance;

    /**
     * 执行指令回调业务
     *
     * @param array $query 请求参数
     * @return void
     */
    public function handle($query)
    {
        // 执行业务
        Gateway::sendToCurrentClient('Wellcome LAF Service!');
    }
}
