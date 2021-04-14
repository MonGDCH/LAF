<?php

namespace Laf\hook\db;

use Laf\Log;
use mon\orm\db\Connection;

/**
 * 记录SQL
 */
class Record
{
    /**
     * 记录SQl
     *
     * @param Connection $connect 链接实例
     * @param mixed $option 参数
     * @return void
     */
    public function handle($option, $connect)
    {
        Log::instance()->sql($connect->getLastSql());
    }
}
