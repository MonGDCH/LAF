<?php
namespace Laf\hook;

use mon\factory\Container;

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
	 * @param  [type] $error 错误信息
	 * @return [type]        [description]
	 */
	public function handler($error)
	{
		$log = "file: {$error['file']} line: {$error['line']} level: {$error['level']} {$error['message']}";
		Container::get('log')->error($log)->save();
	}
}
