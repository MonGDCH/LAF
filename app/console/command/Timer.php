<?php

namespace app\console\command;

use Workerman\Worker;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;
use app\service\TimerService;
use Workerman\Lib\Timer as WorkermanTimer;

/**
 * 基于workerman的定时器, 业务操作回调\app\service\TimerService类
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Timer extends Command
{
    /**
     * 执行间隔时间，执行到0.001
     *
     * @var float
     */
    protected $interval = 10;

    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer exit状态码
     */
    public function execute($in, $out)
    {
        $worker = new Worker();
        $worker->count = 1;
        $worker->onWorkerStart = function ($task) {
            // 启动定时器
            $id = null;
            $id = WorkermanTimer::add($this->interval, [TimerService::instance(), 'handle'], [&$id]);
        };

        Worker::runAll();
        return 0;
    }
}
