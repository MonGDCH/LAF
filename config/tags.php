<?php

/*
|--------------------------------------------------------------------------
| 钩子回调配置文件
|--------------------------------------------------------------------------
| 定义钩子回调信息
|
*/
return [
    'app'       => [
        // 应用初始化
        'bootstrap'     => [
            \Laf\hook\app\Bootstrap::class
        ],
        // 应用执行
        'run'           => [],
        // 执行回调前
        'beforAction'   => [],
        // 执行回调后
        'afterAction'   => [],
        // 响应结果输出前
        'beforSend'     => [],
        // 响应结果输出后
        'afterSend'     => [],
        // 应用结束
        'end'           => [
            \Laf\hook\app\End::class
        ],
        // 应用错误
        'error'         => [
            \Laf\hook\app\Error::class
        ],
    ],
    // 数据库事件监听
    'database'  => [
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
