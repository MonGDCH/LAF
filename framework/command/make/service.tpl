<?php

namespace app\service;

use service\Result;
use service\Service;
use mon\util\Instance;
use GatewayWorker\Lib\Gateway;

/**
 * %s 服务
 *
 * Class %s
 * @copyright %s mon-console
 * @version 1.0.0
 */
class %s implements Service
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
    }
}
