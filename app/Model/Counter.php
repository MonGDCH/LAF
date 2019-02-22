<?php
namespace App\Model;

use mon\Model;
use FApi\Container;
use FApi\traits\Instance;

/**
 * 计数器操作
 *
 * @see gdmonlam\storage\data\counter.sql
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Counter extends Model
{
    use Instance;

    /**
     * 操作表
     *
     * @var string
     */
    protected $table = 'cnt_mark';

    /**
     * 计数记录表
     *
     * @var string
     */
    protected $record_table = 'cnt_record';

    /**
     * 数据库配置
     *
     * @var [type]
     */
    protected $config = [
        // 服务器地址
        'host'            => '127.0.0.1',
        // 数据库名
        'database'        => 'counter',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => 'root',
        // 端口
        'port'            => '3306',
    ];

    /**
     * 增加计数
     * 注意： 已创建计数器标志索引，则设置pond计数池无效
     * 
     * @param [type]  $key1 标志位1
     * @param [type]  $key2 标志位2
     * @param integer $step 步长
     * @param integer $pond 计数池，访问量越大，则设置的计数池数应越大
     */
    public function addCounter($key1, $key2, $step = 1, $pond = 10)
    {
        if($pond < 1){
            $this->error = '最少要有1个计数池';
            return false;
        }
        // 获取标志位
        $markInfo = $this->getMark($key1, $key2);
        // 不存在标志位，创建标志位
        if(!$markInfo){
            $mark_id = $this->createMark($key1, $key2, $pond);
            // 创建标志位失败
            if(!$mark_id){
                return false;
            }
        }
        else{
            $mark_id = $markInfo['id'];
            $pond = $markInfo['ponds'];
        }

        $pond = intval($pond) - 1;
        $query = "INSERT INTO `{$this->record_table}` (`mark_id`, `pond`, `count`, `upadte_time`, `create_time`) VALUES (?, RAND()*{$pond}, ?, ?, ?) ON DUPLICATE KEY UPDATE `count` = `count` + ?, `upadte_time` = ?";

        $exec = $this->execute($query, [$mark_id, $step, $_SERVER['REQUEST_TIME'], $_SERVER['REQUEST_TIME'], $step, $_SERVER['REQUEST_TIME']]);
        Container::get('log')->sql('add counter => '.$this->getLastSql());
        if(!$exec){
            $this->error = '记录计数失败';
            return false;
        }

        return true;
    }

    /**
     * 查询计数
     * 注意： pond计数池数需要与增加计数的pond值相同
     *
     * @param  [type]  $key1 标志位1
     * @param  [type]  $key2 标志位2
     * @param  integer $pond 计数池
     * @return [type]        [description]
     */
    public function queryCounter($key1, $key2, $pond = 10)
    {
        $info = $this->getMark($key1, $key2);
        if(!$info){
            return false;
        }

        $count = $this->table($this->record_table)->where('mark_id', $info['id'])->sum('count');
        if($count === false){
            $this->error = '查询失败';
            return false;
        }

        return $count;
    }

    /**
     * 创建计数器标志索引
     *
     * @param  [type]  $key1   标志位1
     * @param  [type]  $key2   标志位2
     * @param  integer $ponds  采用的计数池数量
     * @param  string  $remark [description]
     * @param  integer $uid    [description]
     * @param  string  $str    [description]
     * @return [type]          [description]
     */
    public function createMark($key1, $key2, $ponds = 10, $remark = '', $uid = 0, $str = '')
    {
        $info = [
            'key1'          => $key1,
            'key2'          => $key2,
            'ponds'         => $ponds,
            'remark'        => $remark,
            'uid'           => $uid,
            'str'           => $str,
            'create_time'   => $_SERVER['REQUEST_TIME'],
        ];

        $id = $this->insert($info, false, true);
        if(!$id){
            $this->error = '创建计数器标志位失败';
            return false;
        }

        return $id;
    }

    /**
     * 获取计数器标志索引
     *
     * @param  [type] $key1 [description]
     * @param  [type] $key2 [description]
     * @return [type]       [description]
     */
    public function getMark($key1, $key2)
    {
        $info = $this->where('key1', $key1)->where('key2', $key2)->where('status', 1)->find();
        if(!$info){
            $this->error = '计数器标志位不存在';
            return false;
        }

        return $info;
    }
}