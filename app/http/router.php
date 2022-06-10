<?php
/*
|--------------------------------------------------------------------------
| 定义应用请求路由
|--------------------------------------------------------------------------
| 使用$router可直接定义路由，或通过Route类进行注册
|
*/

use FApi\Route;

Route::instance()->any('/', '\app\http\controller\IndexController@index');

// 滑动验证码图片
Route::instance()->get('/verify/img', function (\app\libs\verify\Verify $verify) {
    return $verify->setConfig([
        'bg_width'      => 300,
        'bg_height'     => 160,
        'mark_width'    => 50,
        'mark_height'   => 50,
    ])->create();
});
