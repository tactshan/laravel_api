<?php

namespace App\Http\Controllers\User;



use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController
{
    public $redis_h_u_key = 'h:user_token_uid:';
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

    public function phoneAPI()
    {
        //接受邮箱号和密码
        //使用curl传输到passprot上进行授权登录
        $url = 'http://passprot.tactshan.com/phone/login_data';
        $ch = curl_init($url);
        $info=[
            'email'=>$_POST['email'],
            'pwd'=>$_POST['pwd']
        ];

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$info);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $rs = curl_exec($ch);
        $data = json_decode($rs);
        if($data->code==40001){
            $response_data=[
                'code'=>40001,
                'msg'=>'False'
            ];
            echo json_encode($response_data);die;
        }
        $token = $data->token;
        $uid = $data->uid;
        //验证token
        $key=$this->redis_h_u_key.$uid;
        $r_token=Redis::hget($key,'app_token');
        if($r_token==$token){
            $response_data=[
              'code'=>0,
              'msg'=>'Login success!',
                'uid'=>$uid,
              'token'=>$token
            ];
        }else{
            $response_data=[
                'code'=>40002,
                'msg'=>'False'
            ];
        }
        echo json_encode($response_data);
    }

    /**
     * 退出
     */
    public function web_quit()
    {
        $uid = $_GET['uid'];
        $type = $_GET['type'];
        if($type=='1'){
            $token_type = 'web_token';
        }else{
            $token_type = 'app_token';
        }
        $key=$this->redis_h_u_key.$uid;
        $r_token=Redis::hdel($key,$token_type);
    }

    /**
     * app用户列表
     */
    public function phoneUserDate(Request $request)
    {
        //验证用户token
        $token=$request->post('token');
        $uid=$request->post('uid');
//        echo 'token:'.$toekn.'uid:'.$uid;die;
        $token_type = 'app_token';
        $key=$this->redis_h_u_key.$uid;
        $server_token=Redis::hget($key,$token_type);
        if($token!==$server_token){
            echo '身份验证失败！';die;
        }
        //获取用户数据信息
        $user_data = UserModel::all()->toArray();
        $info =[
          'data'=>$user_data
        ];
        echo json_encode($info);
//        var_dump($user_data);
    }
}
