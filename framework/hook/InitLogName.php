<?php
namespace Laf\hook;

use FApi\Container;

/**
 * 定义设置日志保存名称
 */
class InitLogName
{
	/**
	 * 钩子回调
	 *
	 * @return [type] [description]
	 */
	public function handler($app)
	{
		$name = '';
		if(is_string($app->controller)){
			list($controller, $action) = explode('@', $app->controller);
			$name = strtolower(basename($controller));
			$name = str_replace('app\http\controller\\', '', $name);
		}
		Container::get('log')->register(['logName' => $name]);
	}
}