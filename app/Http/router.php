<?php
/*
|--------------------------------------------------------------------------
| 定义应用请求路由
|--------------------------------------------------------------------------
| 使用$router可直接定义路由，或通过Route类进行注册
|
*/

// 首页

use app\provider\Mailer;

$router->any('/', 'app\http\controller\Index@index');

$router->any('/test', function(Mailer $mail){
    $send = $mail->send('test', 'Hello Mon', ['985558837@qq.com']);
    debug($send);
});