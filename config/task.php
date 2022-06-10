<?php

/*
|--------------------------------------------------------------------------
| workerman异步通信服务配置文件
|--------------------------------------------------------------------------
| 定义基于channel异步task服务配置信息
|
*/
return [
    // 服务端监听IP
    'server_ip'     => '0.0.0.0',
    // 客户端监听IP
    'client_ip'     => '127.0.0.1',
    // 端口
    'port'          => 2206,
    // 指令回调映射
    'cmd'           => [
        'email'         => [
            // 基于队列机制
            'type'      => 'queue',
            'callback'  => \app\console\task\EmailTask::class
        ],
        // 'test'         => [
        //     // 基于事件机制
        //     'type'      => 'event',
        //     'callback'  => \app\console\channel\Test::class
        // ],
    ],
];
