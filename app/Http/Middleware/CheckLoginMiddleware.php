<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLoginMiddleware
{
    public function handle($request, Closure $next)
    {
//        setcookie('uid','1',time()+86400,'/','',false,true);
//        setcookie('token','6ad38950977cd24a1ed6',time()+86400,'/','',false,true);
//        die;
       if(isset($_COOKIE['token']) && isset($_COOKIE['uid'])){
           //验证token
           $key = 'h:user_token_u:'.$_COOKIE['uid'];
           $token = Redis::hget($key,'token');
           if($_COOKIE['token']!==$token){
               //有效
               $request->attributes->add(['is_login'=>1]);
           }else{
               //无效
               $request->attributes->add(['is_login'=>0]);
           }
       }else{
           //未登录
           $request->attributes->add(['is_login'=>0]);
       }
        return $next($request);
    }
}
