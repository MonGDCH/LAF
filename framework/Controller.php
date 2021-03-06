<?php

namespace Laf;

use FApi\Request;
use mon\util\Container;

/**
 * 控制器基类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
abstract class Controller
{
    /**
     * 服务容器
     *
     * @var Container
     */
    protected $container;

    /**
     * 请求类实例
     *
     * @var Request
     */
    protected $request;

    /**
     * 返回数据类型
     *
     * @var string
     */
    protected $dataType = 'json';

    /**
     * 响应头
     *
     * @var array
     */
    protected $headers = [];

    /**
     * 允许跨域的域名
     *
     * @var array
     */
    protected $allowOrigin = [];

    /**
     * 允许跨域的请求方式
     *
     * @var array
     */
    protected $allowMethods = [];

    /**
     * 二级设置视图路径
     *
     * @var string
     */
    protected $viewPath = '';

    /**
     * 视图输出数据
     *
     * @var array
     */
    protected $out = [];

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->container = Container::instance();
        $this->request = Request::instance();
        if (!empty($this->viewPath)) {
            $this->container->view->setPath($this->viewPath);
        }
    }

    /**
     * 返回错误信息
     *
     * @param string $msg       描述信息
     * @param array  $data       结果集
     * @param array  $extend     扩展数据
     * @param array  $headers    响应头
     * @return void
     */
    protected function error($msg, $data = [], $extend = [], $headers = [])
    {
        if (!$this->container->request->isAjax()) {
            return $this->abort($msg, 403);
        }
        return $this->dataReturn(0, $msg, $data, $extend, $headers);
    }

    /**
     * 返回成功信息
     *
     * @param string $msg       描述信息
     * @param array  $data       结果集
     * @param array  $extend     扩展数据
     * @param array  $headers    响应头
     * @return void
     */
    protected function success($msg, $data = [], $extend = [], $headers = [])
    {
        return $this->dataReturn(1, $msg, $data, $extend, $headers);
    }

    /**
     * 封装的json返回
     *
     * @param  integer $code    状态码
     * @param  string  $msg     描述信息
     * @param  array   $data    结果集
     * @param  array   $extend  扩展字段
     * @return string           Json数据
     */
    protected function dataReturn($code, $msg, $data = [], $extend = [], $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        if (!empty($this->allowOrigin)) {
            $origin = implode(',', (array) $this->allowOrigin);
            $this->headers['Access-Control-Allow-Origin'] = $origin;
        }

        if (!empty($this->allowMethods)) {
            $method = strtoupper(implode(',', (array) $this->allowMethods));
            $this->headers['Access-Control-Allow-Methods'] = $method;
        }

        return $this->container->url->result($code, $msg, $data, $extend, $this->dataType, $this->headers);
    }

    /**
     * 错误页面
     *
     * @param string $msg
     * @param integer $code
     * @return void
     */
    protected function abort($msg, $code = 404)
    {
        $html = $this->container->view->display(__DIR__ . '/abort', ['code' => $code, 'msg' => $msg]);
        return $this->container->url->abort(200, $html);
    }

    /**
     * 模版赋值
     *
     * @param mixed $key    赋值数据
     * @param mixed $value  值
     * @return void
     */
    protected function assign($key, $value = null)
    {
        return $this->container->view->assign($key, $value);
    }

    /**
     * 输出视图
     *
     * @param string $path  视图名称
     * @param array $data   视图数据
     * @return string
     */
    protected function fetch($path = '', $data = [])
    {
        $this->out = array_merge($this->out, $data);
        return $this->container->view->fetch($path, $this->out);
    }
}
