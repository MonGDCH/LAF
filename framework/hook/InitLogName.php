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
			$ctrl = strtolower($controller);
			$names = str_replace('app\http\controller\\', '', $ctrl);
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $names);
			
		}
		Container::get('log')->register(['logName' => $name]);
	}
}