<?php

namespace Laf\hook\app;

use Laf\Log;

/**
 * 应用错误钩子
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Error
{
	/**
	 * 错误回调钩子
	 *
	 * @param \Exception $error 错误信息
	 * @return void
	 */
	public function handler($excepoton)
	{
		$log = 'file: ' . $excepoton->getFile() . ' line: ' . $excepoton->getLine() . ' message: ' . $excepoton->getMessage();
		Log::instance()->error($log)->save();
	}
}
