<?php

/*
|--------------------------------------------------------------------------
| 注册容器服务
|--------------------------------------------------------------------------
| 注册容器服务，方便调用
|
*/
return [
	/**
	 * 全局通用服务组件
	 */
	'url'			=> Laf\plug\Url::class,
	'page'			=> Laf\plug\Page::class,
	'view'			=> Laf\plug\View::class,
	'file'			=> Laf\plug\File::class,
	'lang'			=> Laf\plug\Lang::class,
	'uploadImg'		=> Laf\plug\UploadImg::class,
	'date'			=> Laf\plug\Date::class,

	'session'		=> Laf\lib\Session::class,
	'cookie'		=> Laf\lib\Cookie::class,
	'cache'			=> Laf\lib\Cache::class,
	'validate'		=> Laf\lib\Validate::class,
	'redis'			=> Laf\lib\Redis::class,
	'log'			=> Laf\lib\Log::instance(), 		// 日志类做了单例处理，所以这里直接注册日志实例
	'captcha'		=> Laf\lib\Captcha::class,
	'oauth'			=> Laf\lib\OAuth::class,

	/**
	 * 中间件服务组件
	 */
	'middle'		=> [
		'home'		=> App\Http\Middleware\Home::class
	],

	/**
	 * 后置件服务组件
	 */
	'append'		=> [

	],
];