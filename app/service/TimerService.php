<?php

namespace app\service;

use mon\util\Instance;
use Workerman\Lib\Timer;

/**
 * Timer定时器服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class TimerService
{
    use Instance;

    protected $i = 0;

    /**
     * socket通信业务处理 
     *
     * @param string $client_id 客服端链接ID
     * @param string $message 通信讯息
     * @return void
     */
    public function handle($id)
    {
        if ($this->i >= 5) {
            Timer::del($id);
            echo 'Exit Timer' . PHP_EOL;
            return;
        }
        echo time() . ' - ' . $id . PHP_EOL;
        $this->i++;
    }
}
