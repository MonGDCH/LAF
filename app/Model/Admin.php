<?php
namespace App\Model;

use mon\Model;

/**
 * 管理员模型
 */
class Admin extends Model
{
	/**
	 * 操作表
	 *
	 * @var string
	 */
	protected $table = 'mon_admin';

	/**
	 * 获取管理员secret
	 *
	 * @param  [type] $openid [description]
	 * @return [type]         [description]
	 */
	public function getSecret($openid)
	{
		$info = $this->where('openid', $openid)->find();
		if(!$info){
			$this->error = '用户不存在Secret';
			return false;
		}

		return $info['google_secret'];
	}
}