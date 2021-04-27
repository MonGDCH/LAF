<?php

namespace app\model;

use mon\orm\Model;
use mon\util\Instance;

/**
 * %s 模型
 *
 * Class %s
 * @copyright %s mon-console
 * @version 1.0.0
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
     * @var array
     */
    protected $insert = ['create_time', 'update_time'];

    /**
     * 更新自动写入字段
     *
     * @var array
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
     * @param mixed $val 默认值
     * @param array  $row 列值
     * @return string
     */
    protected function setCreateTimeAttr($val)
    {
        return time();
    }

    /**
     * 自动完成update_time字段
     * 
     * @param mixed $val 默认值
     * @param array  $row 列值
     * @return string
     */
    protected function setUpdateTimeAttr($val)
    {
        return time();
    }
}
