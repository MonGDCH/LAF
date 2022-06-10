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
		'logPath'      	=> RUNTIME_PATH . '/log',
		'rollNum'      	=> 3,
		'logName'      	=> '',
		'splitLine'     => '======================================================',
	],

	/**
	 * cache文件缓存
	 */
	'cache'		=> [
		'type'			=> 'file',
		'expire'        => 0,
		'cache_subdir'  => true,
		'prefix'        => '',
		'path'          => RUNTIME_PATH . '/cache',
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
	 * 文件上传配置
	 */
	'upload'	=> [
		// 保存路根径
		'root'		=> ROOT_PATH . '/public',
		// 保存目录
		'save'		=> 'upload',
		// 最大尺寸, 10M
		'maxSize'	=> 10000000,
		// 允许上传文件类型
		'exts'		=> ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'mp4', 'avi', 'mkv', 'flv', 'rmvb', 'pdf', 'md'],
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
	]
];
