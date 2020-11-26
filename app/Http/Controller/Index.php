<?php

namespace app\http\controller;

use Laf\Controller;

/**
 * 首页控制器
 */
class Index extends Controller
{
    /**
     * 首页
     *
     * @return void
     */
    public function index()
    {
        return $this->fetch('index');
    }
}
