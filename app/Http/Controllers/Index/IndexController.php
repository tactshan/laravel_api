<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class IndexController
{
    //
    public function index()
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $data=[
          'request_url'=>$url
        ];
        return view('index.index',$data);
    }
}
