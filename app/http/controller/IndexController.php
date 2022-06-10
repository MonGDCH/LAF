<?php

namespace app\http\controller;

use Laf\Controller;

/**
 * 首页控制器
 */
class IndexController extends Controller
{
    /**
     * 首页
     *
     * @return string
     */
    public function index()
    {
        return 'Hello LAF!';
    }
}
