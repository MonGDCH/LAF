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
	'lang'			=> Laf\plug\Lang::class,
	'uploadImg'		=> Laf\plug\UploadImg::class,
    'oauth'         => Laf\plug\OAuth::class,
	'log'			=> Laf\plug\Log::instance(),
];