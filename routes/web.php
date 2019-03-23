<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//首页
$router->get('/user','User\UserController@test');

$router->get('/index','Index\IndexController@index');
//用户注册
$router->post('phone/phone_register','User\UserController@user_register');
//用户登录
$router->post('phone/phone_login','User\UserController@userLogin');
//携带token
$router->post('/user/user_vip','User\UserController@vip');
//接口防刷
$router->post('/api/refresh','User\UserController@refresh_rate');
//设计信息加密
$router->get('/api/encrypt','User\UserController@encrypt');
//非对称加密测试
$router->post('/api/asymmetric','User\UserController@checkPublic');

$router->post('phone/phone_api','User\UserController@phoneAPI');

////app用户列表
//$router->get('phone/phone_api','User\UserController@phoneAPI');

//退出
$router->get('/web/web_quit','User\UserController@web_quit');
