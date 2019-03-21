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
        $data=[
          'request_url'=>$url,
            'is_login'=>$is_login
        ];
        return view('index.index',$data);
    }
}
