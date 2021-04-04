<?php
/*
|--------------------------------------------------------------------------
| 系统指令配置文件
|--------------------------------------------------------------------------
| 定义控制台指令
|
*/
return [
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
    'make'      => [
        'handle'    => Laf\command\Make::class,
        'desc'      => 'Make File Command',
    ],
    // 启动socket服务
    'socket'      => [
        'handle'    => app\console\command\Socket::class,
        'desc'      => 'Runing Socket Service',
    ],
    // 指令名称
    'test'      => [
        // 指令回调, [class|function]
        'handle'    => app\console\command\Test::class,
        // 指令描述
        'desc'      => 'This is Test Command.',
        // 路由别名, 调用：-t
        'alias'     => 't'
    ]
];
