<?php

namespace App\Http\Controllers\User;



use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController
{
    public $redis_h_u_key = 'h:user_token_u:';
    public $public_key = './key/public_key.key';
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

    public function user_register()
    {
        $data=$_POST;
        $email = $data['email'];
        $pwd = $data['pwd'];
        if(empty($email)){
            echo '邮箱不能为空！';
            exit;
        }
        $userInfo=UserModel::where(['email'=>$email])->first();
        if(!empty($userInfo)){
        echo '该邮箱已存在';
        exit;
    }
        $insertData=[
            'email'=>$email,
            'pwd'=>$pwd
        ];
        $uid = UserModel::insertGetId($insertData);
        if($uid){
            echo '1';
        }
    }


    //用户登录
    public function userLogin(Request $request)
    {
//        echo json_encode($_GET);die;
       $email = $_POST['email'];
       $pwd = $_POST['pwd'];
        $where=[
            'email'=>$email,
            'pwd'=>$pwd
        ];
        $data=UserModel::where($where)->first();
        if(empty($data)){
            echo '账号或密码错误1';die;
        }
       //登陆成功验证用户信息
       $uid = $data->uid;
       $str = time().$uid.mt_rand(1111,9999);
       $token=substr(md5($str),10,20);

       //保存到redis中
       $key = $this->redis_h_u_key.$uid;
       $res = Redis::hSet($key,'token',$token);
       if($res){
           echo '1';
       }
       Redis::expire($key,3600*24*7);
    }

    //识别用户信息
    public function vip(Request $request)
    {
        print_r($_SERVER);die;
        print_r($_SERVER['HTTP_TOKEN']);die;
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

    public function server_test()
    {

    }


    //接口防刷
    public function refresh_rate()
    {
        $time = $_GET['t'];
        $key = $key = 'Tactshan';
        $sort = 'xxxxx';
        $iv = substr(md5($time.$sort),5,16);
        $ssh_str = $_POST['ssh_str'];
        $de_ssh_str = openssl_decrypt($ssh_str,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        $str = 'Server received!!! 密文：'.$ssh_str.';译文：'.$de_ssh_str;
        //加密服务器的响应
        $now = time();
        $res_vi = substr(md5($now.$sort),5,16);
        $response_str = openssl_encrypt($str,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$res_vi);
        $data = base64_encode($response_str);
        $info=[
          't' =>$now,
            'data'=>$data
        ];
        echo json_encode($info);

    }

    /**
     * 设计加密
     */
    public function encrypt()
    {
     //
        echo 'test';
    }

    /**
     * 使用公钥验证签名
     */
    public function checkPublic()
    {
        $sign = $_POST['sign'];
        $data = $_POST['data'];
        $data = 'Hello word!';
        $public_key = file_get_contents($this->public_key);
        $result = openssl_verify($data, base64_decode($sign), openssl_get_publickey($public_key), OPENSSL_ALGO_SHA256); //验证签名
        var_dump($result);
    }

    public function test02()
    {
        return view('test.test');
    }
}
