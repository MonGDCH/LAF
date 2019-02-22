<?php
namespace App\Http\Middleware;

use FApi\traits\Jump;

/**
 * 控制器中间件
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Home
{
    use Jump;

	/**
	 * 中间件回调
     * 
	 * @return [type] [description]
	 */
	public function handler($vars, $app)
	{
        $token = $app->request->server('HTTP_X_AUTH_TOKEN');
        if(empty($token)){
            return $this->result(403, 'please login');
        }
        $OAuth = \App\Libs\OAuth::get('api');
        $info = $OAuth->checkToken($token);
        if(!$info){
            return $this->result(403, $OAuth->getError());
        }

        define('__USERID__', $info['user']);

		return $app->next();
	}
}