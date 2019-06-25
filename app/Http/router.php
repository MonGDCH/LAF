<?php
/*
|--------------------------------------------------------------------------
| 定义应用请求路由
|--------------------------------------------------------------------------
| 使用$router可直接定义路由，或通过Route类进行注册
|
*/

$router->get('/', function () {
    $composer_config = json_decode(file_get_contents(ROOT_PATH . '/composer.json'), true);
    return 'Hello LAF! Version ' . $composer_config['version'];
});
