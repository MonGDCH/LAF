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
	'time_zone'			=> 'PRC',

	/**
	 * 日志
	 */
	'log'		=> [
		'maxSize'      	=> 20480000,
        'logPath'      	=> ROOT_PATH . '/storage/log',
        'rollNum'      	=> 3,
        'logName'      	=> '',
	],

	/**
	 * OAuth权限控制
	 */
	'oauth'		=> [
		'salt'		=> 'mon123465789',
		'token_key'	=> 'mon_fapi_key',
		'token_life'=> 86400,
	],

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
        'path'          => ROOT_PATH . 'storage/cache',
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
		'expire'	=> '',
		'secure'	=> '',
		'httponly'	=> ''
	],

	/**
	 * WxApi配置
	 */
	'wxApp'		=> [
		'appid'		=> 'wx4a89252963af9046',
		'secret'	=> 'd940fb1f187f7ffec4f616c5a3c2d2b9'
	]
];