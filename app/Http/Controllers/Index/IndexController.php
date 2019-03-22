<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class IndexController
{
    //
    public function index(Request $request)
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $is_login = $request->get('is_login');
        if(isset($_COOKIE['uid'])){
            $uid = $_COOKIE['uid'];
        }else{
            $uid = '';
        }
        $data=[
          'request_url'=>$url,
            'is_login'=>$is_login,
            'uid'=>$uid
        ];
        return view('index.index',$data);
    }
}
