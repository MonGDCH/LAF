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
	'session'	=> Laf\Session::class,
	// cookie
	'cookie'	=> Laf\Cookie::class,
	// 文件缓存
	'cache'		=> Laf\Cache::class,
	// JWT权限控制
	'jwt'		=> Laf\Jwt::class,
	// RBAC权限控制
	'auth'		=> Laf\Rbac::class,
];
