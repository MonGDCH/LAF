<?php

namespace Laf\hook\app;

use Laf\provider\Log;

/**
 * 定义设置日志保存名称
 */
class InitLogName
{
	/**
	 * 钩子回调
	 *
	 * @param mixed $app	App实例
	 * @return void
	 */
	public function handler($app)
	{
		$name = '';
		if (is_string($app->controller)) {
			list($controller, $action) = explode('@', $app->controller);
			$ctrl = strtolower($controller);
			$names = str_replace('app\http\controller\\', '', $ctrl);
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $names);
		}
		Log::instance()->register(['logName' => $name]);
	}
}
