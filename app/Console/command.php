<?php

/*
|--------------------------------------------------------------------------
| 指令配置文件
|--------------------------------------------------------------------------
| 定义控制台指令
|
*/
return [
    // 指令名称
    // 'router'    => [
    //     // 指令回调, [class|function]
    //     'handle'    => Laf\command\Router::class,
    //     // 指令描述
    //     'desc'      => 'Router dependent instruction.',
    //     // 路由别名, 调用：-r
    //     'alias'     => 'r'
    // ],

    // 启动内置服务器
    'server'    => [
        'handle'    => Laf\command\Server::class,
        'desc'      => 'Built-in HTTP server',
    ],
    // 路由指令
    'router'    => [
        'handle'    => Laf\command\Router::class,
        'desc'      => 'Router dependent instruction.',
    ],
    // 查看配置指令
    'config'    => [
        'handle'    => Laf\command\Config::class,
        'desc'      => 'Check the configuration',
    ],
    // 创建文件
    'make'    => [
        'handle'    => Laf\command\Make::class,
        'desc'      => 'Make File Command',
    ],
    // 自定义指令
    'test'    => App\Console\Command\Test::class,
];
