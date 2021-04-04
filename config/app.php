<?php

/*
|--------------------------------------------------------------------------
| 配置文件
|--------------------------------------------------------------------------
| 定义应用配置信息
|
*/
return [
	/**
	 * 应用时区
	 */
	'time_zone'	=> 'PRC',

	/**
	 * 日志
	 */
	'log'		=> [
		'maxSize'      	=> 20480000,
		'logPath'      	=> ROOT_PATH . '/storage/log',
		'rollNum'      	=> 3,
		'logName'      	=> '',
		'splitLine'     => '======================================================',
	],

	/**
	 * cache文件缓存
	 */
	'cache'		=> [
		'expire'        => 0,
		'cache_subdir'  => true,
		'prefix'        => '',
		'path'          => ROOT_PATH . '/storage/cache',
		'data_compress' => false,
	],

	/**
	 * cookie配置
	 */
	'cookie'	=> [
		'prefix'    => '',
		'expire'    => 0,
		'path'      => '/',
		'domain'    => '',
		'secure'    => false,
		'httponly'  => '',
		'setcookie' => true,
	],

	/**
	 * session配置
	 */
	'session'	=> [
		'prefix'	=> '',
		'expire'	=> '10800',
		'secure'	=> '',
		'httponly'	=> ''
	],

	/**
	 * jwt权限控制
	 */
	'jwt'		=> [
		// 加密key
		'key'	=> 'lkjghssosklqpworiqlkdshlkjf',
		// 加密算法
		'alg'	=> 'HS256',
		// 有效时间
		'exp'	=> 7200,
		// 签发单位
		'iss'	=> 'mon-admin',
	],

	/**
	 * RBAC权限控制
	 */
	'rbac'		=> [
		// 权限开关
		'auth_on'           => true,
		// 用户组数据表名     
		'auth_group'        => 'auth_group',
		// 用户-用户组关系表
		'auth_group_access' => 'auth_access',
		// 权限规则表
		'auth_rule'         => 'auth_rule',
		// 超级管理员权限标志
		'admin_mark'        => '*',
	],

	/**
	 * app业务钩子配置
	 */
	'tags'		=> [
		// 应用初始化
		'bootstrap'		=> [
			\Laf\hook\app\Bootstrap::class
		],
		// 应用执行
		'run'			=> [],
		// 执行回调前
		'beforAction' 	=> [],
		// 执行回调后
		'afterAction' 	=> [],
		// 响应结果输出前
		'beforSend'		=> [],
		// 响应结果输出后
		'afterSend'		=> [],
		// 应用结束
		'end'			=> [
			\Laf\hook\app\End::class
		],
		// 应用错误
		'error'			=> [
			\Laf\hook\app\Error::class
		],
	]
];
