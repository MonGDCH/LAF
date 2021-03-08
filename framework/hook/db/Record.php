<?php

namespace Laf\hook\db;

use mon\util\Container;

/**
 * 记录SQL
 */
class Record
{
    /**
     * 记录SQl
     *
     * @param mixed $option    参数
     * @param mixed $connect   链接实例
     * @return void
     */
    public function handle($option, $connect)
    {
        Container::get('log')->sql($connect->getLastSql());
    }

    /**
     * 记录链接信息
     *
     * @param mixed $option     参数
     * @param mixed $connect    链接实例
     * @return void
     */
    public function connect($option, $connect)
    {
        Container::get('log')->sql('connect database => ' . var_export($option, true));
    }
}
