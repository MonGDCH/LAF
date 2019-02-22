<?php
namespace App\Model;

use mon\Model;
use Laf\util\Strs;
use FApi\Container;
use FApi\traits\Instance;

/**
 * 用户模型
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Invite extends Model
{
	use Instance;

	/**
	 * 模型操作表
	 *
	 * @var string
	 */
	protected $table = 'pz_invite';

	/**
	 * 验证器
	 *
	 * @var [type]
	 */
	protected $validate;

	/**
     * 新增自动写入字段
     *
     * @var [type]
     */
    protected $insert = ['create_time', 'update_time'];

    /**
     * 更新自动写入字段
     *
     * @var [type]
     */
    protected $update = ['update_time'];

    /**
     * 获取信息
     *
     * @return [type] [description]
     */
    public function getInfo($value, $key = 'code')
	{
		$info = $this->where($key, $value)->find();
		if(!$info){
			$this->error = 'invite not exists';
			return false;
		}

		return $info;
	}

	/**
	 * 新增邀请码
	 *
	 * @param [type]  $code [description]
	 * @param integer $pid  [description]
	 * @param integer $uid  [description]
	 */
	public function add($code, $pid = 0, $uid = 0)
	{
		// 查询邀请码是否已存在
		$exists = $this->getInfo($code);
		if($exists){
			$this->error = 'invite code exists';
			return false;
		}

		$info = [
			'uid'	=> $uid,
			'pid'	=> $pid,
			'code'	=> $code
		];

		$save = $this->save($info);
		if(!$save){
			$this->error = 'add invite code error';
			return false;
		}

		return true;
	}

	/**
	 * 自动完成create_time字段
	 * 
	 * @param [type] $val 默认值
	 * @param array  $row 列值
	 */
	protected function setCreateTimeAttr($val)
	{
		return $_SERVER['REQUEST_TIME'];
	}

	/**
	 * 自动完成update_time字段
	 * 
	 * @param [type] $val 默认值
	 * @param array  $row 列值
	 */
	protected function setUpdateTimeAttr($val)
	{
		return $_SERVER['REQUEST_TIME'];
	}

}