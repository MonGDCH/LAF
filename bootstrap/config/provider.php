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
	'log'			=> Laf\provider\Log::instance(),
	// Session
	'session'		=> Laf\provider\Session::class,
	// cookie
	'cookie'		=> Laf\provider\Cookie::class,
	// 文件缓存
	'cache'			=> Laf\provider\Cache::class,
	// Redis
	'redis'			=> Laf\provider\Redis::class,
	// 视图
	'view'			=> Laf\provider\View::class,
	// JWT权限控制
	'jwt'			=> Laf\provider\Jwt::class,
	// RBAC权限控制
	'auth'			=> Laf\provider\Rbac::class,
	// 树
	'tree'			=> mon\util\Tree::class,
	// 通用工具
	'util'			=> mon\util\Common::class,
	// 邮件
	'mailer'		=> app\provider\Mailer::class,
	// 微信工具
	'wechat'		=> app\provider\Wechat::class
];
