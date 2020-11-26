<?php

namespace service;

/**
 * 服务抽象基类
 */
interface Service
{
    /**
     * 单例实现方法
     *
     * @return static
     */
    public static function instance();

    /**
     * 抽象接口方法
     *
     * @param array $query $请求参数
     * @return void
     */
    public function handle($query);
}
