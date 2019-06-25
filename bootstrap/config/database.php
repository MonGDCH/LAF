<?php

/*
|--------------------------------------------------------------------------
| 数据库配置文件
|--------------------------------------------------------------------------
| 定义数据库链接信息
|
*/
return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'host'            => '127.0.0.1',
    // 数据库名
    'database'        => 'test',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'root',
    // 端口
    'port'            => '3306',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 返回结果集类型
    'result_type'     => PDO::FETCH_ASSOC,
];
