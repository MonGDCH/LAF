<?php

/*
|--------------------------------------------------------------------------
| 获取应用实例
|--------------------------------------------------------------------------
| 这里获取应用实例
|
*/
$app = require_once __DIR__ . '/bootstrap.php';


/*
|--------------------------------------------------------------------------
| 配置数据库
|--------------------------------------------------------------------------
| 命令控制台启动脚本，Mysql链接断开自动重连
|
*/
\mon\orm\Db::reconnect(true);


/*
|--------------------------------------------------------------------------
| 获取控制台应用实例
|--------------------------------------------------------------------------
| 这里获取控制台应用实例
|
*/
$console = \mon\console\App::instance();


/*
|--------------------------------------------------------------------------
| 获取生成指令
|--------------------------------------------------------------------------
| 这里注册可执行的指令
|
*/
$sysCommand = [
    // 优化应用
    'optimize'  => [
        'handle'    => Laf\command\Optimize::class,
        'desc'      => 'optimize application'
    ],
    // 启动内置服务器
    'server'    => [
        'handle'    => Laf\command\Server::class,
        'desc'      => 'Built-in HTTP server',
    ],
    // 路由指令
    'router'    => [
        'handle'    => Laf\command\Router::class,
        'desc'      => 'Router dependent instruction',
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
    ]
];
$userCommand = require APP_PATH . '/console/command.php';
$commands = $sysCommand + $userCommand;


/*
|--------------------------------------------------------------------------
| 注册指令
|--------------------------------------------------------------------------
| 这里注册指令
|
*/
foreach ($commands as $command => $option) {
    if (is_array($option)) {
        $handle = isset($option['handle']) ? $option['handle'] : null;
        if (is_null($handle)) {
            // 指令定义错误
            throw new Exception('[ERROR] register command error, handle required! command: ' . $command, 500);
        }
        $console->add($command, $handle, $option);
    } else {
        $console->add($command, $option);
    }
}

return $console;
