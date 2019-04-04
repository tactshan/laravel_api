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

//app用户列表
$router->post('/phone/phone_u_date','User\UserController@phoneUserDate');

//退出
$router->get('/web/web_quit','User\UserController@web_quit');

//月考
//用户请求首页
$router->get('/month/index','Month\MonthController@index');
//接受用户请求信息
$router->post('/month/user_info','Month\MonthController@user_info');
//后台审核页面
$router->get('/month/audit','Month\MonthController@audit');
//处理审核
$router->get('/month/audit_do','Month\MonthController@audit_do');
//拒绝通过审核
$router->get('/month/audit_no','Month\MonthController@audit_no');
$router->post('/month/audit_no_do','Month\MonthController@audit_no_do');
//生成app_key和app_secret
$router->get('/month/create_key','Month\MonthController@create_key');
$router->get('/month/create_secret','Month\MonthController@create_secret');
//防刷接口测试
$router->post('/month/test_api','Month\MonthController@testAPI');

//API测试
//接受数据
$router->post('/data_transmit','DataTransmit\DataController@store');



