<?php
/*
|--------------------------------------------------------------------------
| 注册容器服务
|--------------------------------------------------------------------------
| 注册容器服务，方便调用
|
*/
return [
	// Session
	'session'	=> Laf\provider\Session::class,
	// cookie
	'cookie'	=> Laf\provider\Cookie::class,
	// 文件缓存
	'cache'		=> Laf\provider\Cache::class,
	// JWT权限控制
	'jwt'		=> Laf\provider\Jwt::class,
	// RBAC权限控制
	'auth'		=> Laf\provider\Rbac::class,
];
