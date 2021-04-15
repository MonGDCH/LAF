<?php

namespace Laf\hook\db;

use Laf\Log;
use mon\orm\db\Connection;

/**
 * 数据库链接事件
 */
class Connect
{
    /**
     * 记录DB链接信息
     *
     * @param Connection $connect
     * @param mixed $config 配置信息
     * @return void
     */
    public function handler($connect, $config)
    {
        Log::instance()->sql('connect database => ' . var_export($config, true));
    }
}
