<?php
namespace App\Http\Middleware;

use FApi\traits\Jump;
use Laf\util\Comm;

/**
 * 微信接口相关前置控制器
 */
class Wx
{
	use Jump;

	/**
	 * 中间件回调
	 * @return [type] [description]
	 */
	public function handler($vars, $app)
	{
		if(!Comm::is_wx()){
			return $this->abort(403);
		}

		return $app->next();
	}
}