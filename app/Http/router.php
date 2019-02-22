<?php
/*
|--------------------------------------------------------------------------
| 定义应用请求路由
|--------------------------------------------------------------------------
| 使用$router可直接定义路由，或通过Route类进行注册
|
*/

// $router->get('/', function(){
// 	return 'Hello LAF! Version '.\FApi\App::VERSION;
// });

$router->get(['path' => '/', 'middleware' => 'middle_home'], 'Index@index');


/**
 * 用户相关接口
 */
$router->group(['path' => '/wx', 'namespace' => 'App\Http\Controller\\', 'middleware' => \App\Http\Middleware\Wx::class], function($router){
	// 登录
	$router->get('/login/{code}', 'Wx@login');

	// 获取Secret
	$router->post('/getSecret', 'Wx@getSecret');
});

/**
 * 博客相关接口
 */
$router->group(['path' => '/blog'], function($router){
    // 查询列表
    $router->get('/query[/{page:[1-9]\d*}]', 'Blog@query');

    // 查看文章
    $router->get('/article/{idx:[1-9]\d*}', 'Blog@article');

    // 查看归档列表
    $router->get('/archive', 'Blog@archive');

    // 查看关于我
    $router->get('/about', 'Blog@about');
});