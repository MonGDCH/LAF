<?php

/*
|--------------------------------------------------------------------------
| 数据库配置文件
|--------------------------------------------------------------------------
| 定义数据库链接信息
|
*/
return [
    // 数据库类型
    'type'          => 'mysql',
    // 服务器地址
    'host'          => '127.0.0.1',
    // 数据库名
    'database'      => '',
    // 用户名
    'username'      => '',
    // 密码
    'password'      => '',
    // 端口
    'port'          => '3306',
    // 数据库编码默认采用utf8
    'charset'       => 'utf8',
    // 事件监听
    'enevt'         => [
        // 链接DB
        'connect'            => [
            \Laf\hook\db\Connect::class
        ],
        // select查询
        'select'            => [],
        // insert查询
        'insert'            => [],
        // delete查询
        'delete'            => [],
        // update查询
        'update'            => [],
        // query全局查询
        'query'             => [
            \Laf\hook\db\Record::class
        ],
        // execute全局指令
        'execute'           => [
            \Laf\hook\db\Record::class
        ],
        // 开启事务
        'startTrans'        => [],
        // 提交事务
        'commitTrans'       => [],
        // 回滚事务
        'rollbackTrans'     => [],
        // 开启事扩库务
        'startTransXA'      => [],
        // 开启预编译XA事务
        'prepareTransXA'    => [],
        // 提交跨库事务
        'commitTransXA'     => [],
        // 回滚跨库事务
        'rollbackTransXA'   => [],
    ]
];
