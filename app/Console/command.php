<?php

/*
|--------------------------------------------------------------------------
| 自定义指令配置文件
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

    // 测试指令
    'test'    => App\Console\Command\Test::class,
];
