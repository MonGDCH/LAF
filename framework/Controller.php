<?php
namespace Laf;

use mon\factory\Container;
use FApi\traits\Jump;

/**
 * 控制器基类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Controller
{
    use Jump;

    /**
     * 服务容器
     *
     * @var [type]
     */
    protected $container;

    /**
     * 请求实例
     *
     * @var [type]
     */
    protected $request;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->container = Container::instance();
        $this->request = $this->container->make('request');
    }

    /**
     * 返回错误信息
     *
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    protected function errorJson($msg, $data = [], $extend = [], $headers = [])
    {
        return $this->result(0, $msg, $data, $extend);
    }

    /**
     * 返回成功信息
     *
     * @param  [type] $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    protected function successJson($msg, $data = [], $extend = [], $headers = [])
    {
        return $this->dataReturn(1, $msg, $data, $extend);
    }

    /**
     * 封装的json返回
     *
     * @param  [type] $code    状态码
     * @param  [type] $msg     描述信息
     * @param  array  $data    结果集
     * @param  array  $extend  扩展字段
     * @return [type]          [description]
     */
    protected function dataReturn($code, $msg, $data = [], $extend = [], $headers = [])
    {
    	$this->headers = array_merge($this->headers, $headers);

        return $this->result($code, $msg, $data, $extend, $this->dataType, $this->headers);
    }
}