<?php

namespace App\Http\Controllers\DataTransmit;


use App\Model\MonthUserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class DataController
{
    public function store(Request $request)
    {
        #接收客户端数据
        $data = $request->all();


    }

    /**
     * 生成app_key
     * @param $id
     * @return bool|string
     */
    public function create_key($id)
    {
        $app_key=substr(md5(mt_rand(11111,99999).$id),0,18);
        return $app_key;
    }

    /**
     * 生成app_secret
     * @param $app_key
     * @return bool|string
     */
    public function create_secret($app_key)
    {
        $app_secret=substr(md5($app_key),0,18);
        return $app_secret;
    }
}
