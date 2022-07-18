<?php

namespace app\console\command;

use Laf\Log;
use mon\env\Config;
use Workerman\Worker;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;
use Workerman\Lib\Timer as WorkermanTimer;

/**
 * 基于workerman的定时器
 * 
 * @requires workerman/workerman
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
        // 定义worker日志名称
        $workerLog = Config::instance()->get('app.log.logPath');
        Worker::$logFile = $workerLog ? ($workerLog . '/workerman.log') : '';
        $worker = new Worker();
        $worker->name = 'MonTimer';
        $worker->count = 1;
        $worker->onWorkerStart = function ($task) {
            // 命令控制台启动脚本，Mysql链接断开自动重连
            Log::instance()->setConfig(['logPath' => RUNTIME_PATH . '/log/timer']);
            Log::instance()->info('Timer service start')->save();
            // 启动定时器
            $id = null;
            $id = WorkermanTimer::add($this->interval, [$this, 'handle'], [&$id]);
        };

        Worker::runAll();
        return 0;
    }

    /**
     * 定时脚本执行回调
     *
     * @param integer $id
     * @return void
     */
    public function handle($id)
    {
        echo $id . PHP_EOL;
    }
}
