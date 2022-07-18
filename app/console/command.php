<?php
/*
|--------------------------------------------------------------------------
| 系统指令配置文件
|--------------------------------------------------------------------------
| 自定义控制台指令
|
*/
return [
    // 启动socket服务
    'socket'      => [
        'handle'    => app\console\command\Socket::class,
        'desc'      => 'Runing Socket Service',
    ],
    // 启动定时器服务
    'timer'      => [
        'handle'    => app\console\command\Timer::class,
        'desc'      => 'Runing timer Service',
    ],
    // 启动任务服务
    'task'      => [
        'handle'    => app\console\command\Task::class,
        'desc'      => 'Runing task Service',
    ],
    // 指令名称
    'test'      => [
        // 指令回调, [class|function]
        'handle'    => app\console\command\Test::class,
        // 指令描述
        'desc'      => 'This is Test Command',
        // 路由别名, 调用：-t
        'alias'     => 't'
    ]
];
