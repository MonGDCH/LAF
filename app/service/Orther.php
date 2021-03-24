<?php

namespace app\service;

use service\Result;
use service\Service;
use mon\util\Instance;
use GatewayWorker\Lib\Gateway;

/**
 * Orther指令处理器
 * 默认用于没有指定存在的指令时使用
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0 2021-03-24
 */
class Orther implements Service
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
        Gateway::sendToClient($_SESSION['clientID'], Result::instance()->data(Result::ERR_CMD_UNRECOGNIZED, Result::getMessgae(Result::ERR_CMD_UNRECOGNIZED)));
    }
}
