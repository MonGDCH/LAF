<?php
/*
|--------------------------------------------------------------------------
| 系统指令配置文件
|--------------------------------------------------------------------------
| 定义控制台指令
|
*/

return array_merge([
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
    ]
], require ROOT_PATH . '/app/Console/command.php');