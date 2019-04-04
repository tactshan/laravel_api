<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class RefreshRateBeforeMiddleware
{
    public function handle($request, Closure $next)
    {
        //防刷条件 同一用户/同一接口/一分钟内20次

        //获取用户访问的uri
        $request_uri = $_SERVER['REQUEST_URI'];
        $uri_hash = substr(md5($request_uri),0,10);
        //获取客户端ip
        $ip=$_SERVER['REMOTE_ADDR'];
        //记录用户的访问次数,存redis
        $key='str:'.$uri_hash.'ip:'.$ip;
        $data=Redis::get($key);

        if(empty($data)){
            $num=Redis::incr($key);
            //设置过期时间
            Redis::expire($key,60);
        }else{
            $num=Redis::get($key);
            $init_num=(int)$num;
            var_dump($init_num);echo '</br>';
            $time=Redis::ttl($key);
            var_dump($time);echo '</br>';
            if($num<20&&$time<60){
                $num=Redis::incr($key);
            }else{
                //记录客户端ip
                $ip_key = 'vicious_ip';
                Redis::sAdd($ip_key,$ip);  //获取SMEMBERS  sadd集合
                Redis::expire($key,600);
                $response=[
                  'error' => '50001', //50001访问频率过高
                  'msg' => 'many request',
                ];
                echo json_encode($response);die;
            }
        }
        return $next($request);
    }
}
