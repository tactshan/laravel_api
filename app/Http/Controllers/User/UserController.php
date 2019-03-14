<?php

namespace App\Http\Controllers\User;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController
{
    public $redis_h_u_key = 'h:user_token_u:';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function test()
    {
        echo __METHOD__;
    }
    //用户登录
    public function userLogin(Request $request)
    {
       $user_name = $request->input('u');
       $pass = $request->input('p');
       $res=true;
       //登陆成功验证用户信息
       if($res){
           $uid = 1000;
           $str = time().$uid.mt_rand(1111,9999);
           $token=substr(md5($str),10,20);

           //保存到redis中
           $key = $this->redis_h_u_key.$uid;
           Redis::hSet($key,'token',$token);
           Redis::expire($key,3600*24*7);
       }else{
           // TODO 登录失败
       }
    }

    //识别用户信息
    public function vip(Request $request)
    {
        print_r($_SERVER['HTTP_TOKEN']);
        echo '</br>';
        $uid=1000;
        $key=$this->redis_h_u_key.$uid;
        $token=Redis::hget($key,'token');
        echo $token;
        if($_SERVER['HTTP_TOKEN']==$token){
            echo "登录成功";
        }else{
            echo "FAIL";
        }

    }
}
