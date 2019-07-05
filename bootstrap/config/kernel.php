<?php

/*
|--------------------------------------------------------------------------
| 注册容器服务
|--------------------------------------------------------------------------
| 注册容器服务，方便调用
|
*/
return [
	// 请求实例
	'request'		=> FApi\Request::instance(),
	// URL请求、结果集操作
	'url'			=> FApi\Url::instance(),
	// 配置信息
	'config'		=> mon\env\Config::instance(),
	// 文件对象
	'file'			=> mon\store\File::class,
	// 日志
	'log'			=> App\Kernel\Log::instance(), 
];