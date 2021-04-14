<?php

namespace Laf\hook\app;

use Laf\Log;

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
		Log::instance()->save();
	}
}
