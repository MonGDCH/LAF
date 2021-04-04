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
\mon\orm\Db::setConfig(['break_reconnect' => true]);


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
| 注册指令
|--------------------------------------------------------------------------
| 这里注册指令
|
*/
$commands = $config->get('command', []);
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
