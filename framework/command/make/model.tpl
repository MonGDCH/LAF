<?php

namespace App\Model;

use mon\orm\Model;
use mon\util\Instance;
use mon\factory\Container;

/**
 * %s 模型
 *
 * Class %s
 * @created for mon-console
 */
class %s extends Model
{
    use Instance;

    /**
     * 操作表
     *
     * @var string
     */
    protected $table = '%s';

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
     * 构造方法
     */
    public function __construct()
    {
        //
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
