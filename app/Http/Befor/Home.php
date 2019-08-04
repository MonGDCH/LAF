<?php
namespace App\Http\Befor;

/**
 * 控制器中间件
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Home
{
	/**
	 * 中间件回调
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
