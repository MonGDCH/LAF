<?php
namespace Laf;

use Laf\Controller;

/**
 * 控制器基类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class ViewController extends Controller
{
    /**
     * 视图
     *
     * @var [type]
     */
    protected $view;

    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();
        $this->view = $this->container->make('view');
    }

    /**
     * 定义模版引擎数据
     *
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function assign($key, $value = null)
    {
        return $this->view->assign($key, $value);
    }

    /**
     * 获取视图内容(补全路径)
     *
     * @param  [type] $value [description]
     * @param  array  $data  [description]
     * @return [type]        [description]
     */
    public function fetch($view, $data = array())
    {
        return $this->view->fetch($view, $data);
    }

    /**
     * 获取视图内容(不补全视图路径)
     *
     * @param  [type] $view [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function display($view, $data = array())
    {
        return $this->view->display($view, $data);
    }
}