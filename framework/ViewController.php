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
}