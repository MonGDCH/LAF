<?php

/*
|--------------------------------------------------------------------------
| 钩子配置文件
|--------------------------------------------------------------------------
| 定义应用执行过程中加载的钩子
|
*/
return [
	// 框架应用对应使用的钩子
	'app'	=> [
		// 应用初始化
		'bootstrap'		=> [
			\Laf\hook\app\Bootstrap::class
		],

		// 应用执行
		'run'			=> [],

		// 执行回调前
		'action_befor' 	=> [],

		// 执行回调后
		'action_after' 	=> [],

		// 响应结果输出前
		'send'			=> [],

		// 应用结束
		'end'			=> [
			\Laf\hook\app\End::class
		],

		// 应用错误
		'error'			=> [
			\Laf\hook\app\Error::class
		],
	],
	// DB模型事件对应使用钩子
	'db'	=> [
		// 链接DB
		'connect'	=> '\Laf\hook\db\Record@connect',
		// select查询
		'select'	=> '',
		// insert查询
		'insert'	=> '',
		// delete查询
		'delete'	=> '',
		// update查询
		'update'	=> '',
		// query全局查询
		'query'		=> '\Laf\hook\db\Record@handle',
		// execute全局指令
		'execute'	=> '\Laf\hook\db\Record@handle'
	],
];
