<?php
namespace Laf\hook;

use FApi\Container;

/**
 * 应用初始化钩子
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Bootstrap
{
	/**
	 * 钩子回调方法
	 *
	 * @return [type] [description]
	 */
	public function handler()
	{
		Container::get('log')->info($this->parseRequest());
	}

	/**
	 * 解析请求为日志信息
	 *
	 * @return [type] [description]
	 */
	protected function parseRequest()
	{
		$server = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
        $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';
        $uri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        return "{$server} {$remote} {$method} {$uri}";
	}
}