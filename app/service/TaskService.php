<?php

namespace app\service;

use Channel\Client;
use mon\env\Config;
use mon\util\Instance;

/**
 * 基于Channel的异步任务服务
 * 
 * @author Mon <985558837@qq.cm>
 * @version 1.0.0
 */
class TaskService
{
    use Instance;

    /**
     * 配置信息
     *
     * @var array
     */
    protected $config;

    /**
     * 构造方法
     */
    protected function __construct()
    {
        $this->config = Config::instance()->get('task');
        Client::connect($this->config['client_ip'], $this->config['port']);
    }

    /**
     * 推送订阅
     *
     * @param string $cmd
     * @param mixed $data
     * @return void
     */
    public function publish($cmd, $data)
    {
        Client::publish($cmd, $data);
    }

    /**
     * 推送队列
     *
     * @param string $cmd
     * @param mixed $data
     * @return void
     */
    public function enqueue($cmd, $data)
    {
        Client::enqueue($cmd, $data);
    }
}
