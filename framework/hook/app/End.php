<?php

namespace Laf\hook\app;

use mon\factory\Container;

/**
 * 应用结束钩子
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class End
{
	/**
	 * 钩子回调
	 *
	 * @return void
	 */
	public function handler()
	{
		// 记录日志
		Container::get('log')->save();
	}
}
