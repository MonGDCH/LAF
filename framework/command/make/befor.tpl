<?php

namespace app\http\middleware;

/**
 * %s 前置中间件
 *
 * Class %s
 * @created for mon-console
 */
class %s
{
	/**
	 * 回调方法
	 *
	 * @param [type] $val	参数
	 * @param [type] $app	APP实例，返回next方法执行后续操作
	 * @return void
	 */
	public function handler($val, $app)
	{
		return $app->next();
	}
}
