<?php
namespace App\Model\blog;

use mon\Model;
use FApi\traits\Instance;

/**
* 博客文章模型
*
* @author Mon
*/
class Article extends Model
{
    use Instance;

	/**
	 * 模型对应表名
	 *
	 * @var string
	 */
	protected $table = 'mon_blog_article';

	/**
	 * 新增自动完成
	 *
	 * @var [type]
	 */
	protected $insert = ['update_time', 'create_time'];

	/**
	 * 修改自动完成
	 *
	 * @var [type]
	 */
	protected $update = ['update_time'];

	/**
	 * 赋值创建时间
	 *
	 * @param [type] $val [description]
	 */
	protected function setCreateTimeAttr($val)
	{
		return $_SERVER['REQUEST_TIME'];
	}

	/**
	 * 格式化创建时间
	 *
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	protected function getCreateTimeAttr($val)
	{
		return date('Y-m-d H:i:s', $val);
	}

	/**
	 * 赋值更新时间
	 *
	 * @param [type] $val [description]
	 */
	protected function setUpdateTimeAttr($val)
	{
		return $_SERVER['REQUEST_TIME'];
	}

	/**
	 * 格式化更新时间
	 *
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	protected function getUpdateTimeAttr($val)
	{
		return date('Y-m-d H:i:s', $val);
	}

	/**
	 * 格式化获取发布时间
	 *
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	protected function getPushTimeAttr($val)
	{
		return ($val == '0') ? '0' : date('Y-m-d H:i:s', $val);
	}

	/**
     * 获取信息
     *
     * @param  [type] $value [description]
     * @param  string $item  [description]
     * @return [type]        [description]
     */
    public function getInfo($value, $item = 'id')
    {
        $info = $this->where($item, $value)->get();
        if(!$info){
            $this->error = '文章不存在';
            return false;
        }

        return $info;
    }

	/**
     * 查询列表
     *
     * @return [type] [description]
     */
    public function queryList(array $option)
    {
        $limit = isset($option['pageSize']) ? intval($option['pageSize']) : 10;
        $page = isset($option['page']) ? intval($option['page']) : 0;
        // 查询
        $list = $this->scope('list', $option)->limit(($page * $limit), $limit)->all();
        $total = $this->scope('list', $option)->count('a.id');

        return [
            'list'      => $list,
            'total'     => $total,
            'pageSize'  => $limit,
            'page'      => $page
        ];
    }

    /**
     * 查询用户列表场景
     *
     * @return [type] [description]
     */
    protected function scopeList($query, $args)
    {
        $cond = $query->table("{$this->table} a")->join('mon_blog_tags b', 'a.type=b.id')->field('a.*, b.name')->order('a.sort', 'desc');

        if(isset($args['status']) && $args['status'] != '' && is_numeric($args['status'])){
            $cond->where('a.status', intval($args['status']));
        }
        if(isset($args['title']) && !empty($args['title'])){
            $cond->where('a.title', 'like', "%{$args['title']}%");
        }
        if(isset($args['start_time']) && is_numeric($args['start_time']) && $args['start_time'] != ''){
            $cond->where('a.create_time', '>=', intval($args['start_time']));
        }
        if(isset($args['end_time']) && is_numeric($args['end_time']) && $args['end_time'] != ''){
            $cond->where('a.create_time', '<=', intval($args['end_time']));
        }

        return $cond;
    }

    /**
     * 获取归档列表
     *
     * @return [type] [description]
     */
    public function archive()
    {
		$res = $this->where('status', 1)->field('id, title, push_time')->order('id', 'desc')->select();

		$data  = [];
		foreach($res as $v)
		{
			$data[] = [
				'id'	=> $v['id'],
				'title'	=> $v['title'],
				'desc'	=> '发布了文章《'.$v['title'].'》',
				'times'	=> date('Y年m月d号', $v['push_time'])
			];
		}

		return $data;
    }
}