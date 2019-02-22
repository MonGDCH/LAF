<?php

/*
|--------------------------------------------------------------------------
| 钩子配置文章
|--------------------------------------------------------------------------
| 定义应用执行过程中加载的钩子
|
*/
return [
	// 应用初始化
	'bootstrap'		=> [
		\Laf\hook\Bootstrap::class,
		// \Laf\hook\Test::class
	],

	// 应用执行
	'run'			=> [],

	// 执行回调前
	'action_befor' 	=> [
		\Laf\hook\InitLogName::class
	],

	// 执行回调后
	'action_after' 	=> [],
	
	// 响应结果输出前
	'send'			=> [],
	
	// 应用结束
	'end'			=> [
		\Laf\hook\End::class
	],
	
	// 应用错误
	'error'			=> [
		\Laf\hook\Error::class
	],
];