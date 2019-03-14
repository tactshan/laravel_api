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

$router->get('/user','User\UserController@test');
//用户登录
$router->post('/user/user_login','User\UserController@userLogin');
//携带token
$router->post('/user/user_vip','User\UserController@vip');