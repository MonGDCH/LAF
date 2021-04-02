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
	 * 数据库配置
	 */
	'database'  => file_exists(__DIR__ . '/database.php') ? require(__DIR__ . '/database.php') : [],

	/**
	 * Redis缓存数据库
	 */
	'redis'		=> [
		'host'       => '127.0.0.1',
		'port'       => 6379,
		'password'   => '',
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
	 * 视图配置
	 */
	'view'		=> [
		// 视图文件目录
		'path'	=> ROOT_PATH . '/app/http/view/',
		// 视图文件后缀
		'ext'	=> 'html'
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

	// 异步长链接服务配置
	'service'		=> file_exists(__DIR__ . '/service.php') ? require(__DIR__ . '/service.php') : [],

	// 邮件配置
	'email'			=> [
		// SMTP服务器
		'host'		=> 'smtp.qq.com',
		// SMTP服务器的远程服务器端口号
		'port'		=> 465,
		// 是否开启ssl
		'ssl'		=> true,
		// 发件人邮箱地址
		'from'		=> '',
		// SMTP发件人名称
		'name'		=> '',
		// SMTP登录账号
		'user'		=> '',
		// SMTP登录密码
		'password'	=> '',
	],
];
