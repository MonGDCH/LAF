<?php

/**
 * 开启调试模式
 */
define('IS_DEBUG', true);

/**
 * 创建APP应用实例
 */
$app = require __DIR__.'/../bootstrap/bootstrap.php';

/**
 * 执行应用, 输出响应结果集
 */
$app->run()->send();