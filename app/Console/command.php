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
    'test'      => [
        // 指令回调, [class|function]
        'handle'    => app\console\command\Test::class,
        // 指令描述
        'desc'      => 'This is Test Command.',
        // 路由别名, 调用：-t
        'alias'     => 't'
    ],
    // 测试指令
    // 'test'    => app\console\command\Test::class,
];
