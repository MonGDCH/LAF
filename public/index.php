<?php

/**
 * 创建APP应用实例
 */
$app = require __DIR__.'/../bootstrap/app.php';

/**
 * 执行应用, 输出响应结果集
 */
$app->run()->send();