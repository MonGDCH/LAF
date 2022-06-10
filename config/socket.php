<?php

/*
|--------------------------------------------------------------------------
| gateway长链接服务配置文件
|--------------------------------------------------------------------------
| 定义异步长链接服务配置信息
|
*/
return [
    // 指令回调映射
    'cmd'   => [
        'test'   => \app\console\socket\TestSocket::class
    ],
    // 链接鉴权
    'safe'  => [
        // 是否开启鉴权
        'auth'      => false,
        // 加密盐秘钥
        'salt'      => 'ws-mon-2021',
        // ticket名称
        'name'      => '_ws_',
        // token名称
        'token'     => '_ws_token_',
        // tokentime名称
        'time'      => '_ws_token_time_',
        // 有效期
        'expire'    => 36000,
    ],
    // geteway 进程
    'gateway' => [
        // gateway名称，status方便查看
        'name'              => 'MonGateway',
        // gateway进程数，gateway进程数建议与cpu核数相同
        'count'             => 1,
        // 本机ip，分布式部署时使用内网ip
        'lanIp'             => '127.0.0.1',
        //内部通讯起始端口，假如$gateway->count = 4，起始端口为4000, 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口，注意：起始端口不要重复
        'startPort'         => 2500,
        // 心跳间隔
        'pingInterval'      => 58,
        // 心跳数据
        'pingData'          => '',
        // 多少心跳间隔时间内客户端未报通信则断开连接（pingInterval * pingNotLimit = 时间间隔）
        'pingNotLimit'      => 0,
        // 服务注册地址
        'registerAddress'   => '127.0.0.1:1239',
        // 链接协议地址
        'address'           => 'websocket://0.0.0.0:8623',
        // context附加参数, 主要用于实现ssl，可使用nginx方向代理实现ssl
        'context'           => [],
        // 传输协议，一般不需要改动，如需使用ssl，则修改为ssl
        'transport'         => 'tcp'
    ],
    // businessworker 进程
    'business' => [
        // worker名称
        'name'              => 'MonBusinessWorker',
        // bussinessWorker进程数量，一般为gateway进程数的2-4倍
        'count'             => 2,
        // 服务注册地址
        'registerAddress'   => '127.0.0.1:1239',
    ],
    // register 服务
    'register' => [
        // 启用register服务，分布式部署时，只在主业务机器启用
        'init'              => true,
        // register名称，status方便查看
        'name'              => 'MonRegister',
        // 链接协议地址, 必须是text协议
        'address'           => 'text://0.0.0.0:1239',
    ],
    // IP白名单
    'whiteList'             => [],
    // 允许发起websocket的域名，需要携带http标识，如：http://demo.test
    'websocket_domain'      => [],
];
