<?php

namespace app\libs;

use Channel\Server;

/**
 * 继承扩展\Channel\Server类，获取worker实例
 */
class ChannelServer extends Server
{
    /**
     * 获取Worker实例
     *
     * @return \Workerman\Worker
     */
    public function getWorker()
    {
        return $this->_worker;
    }
}