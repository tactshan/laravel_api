<?php

namespace App\Http\Controllers\Month;


use App\Model\MonthUserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class MonthController
{
    public $app_key='h:app_key:id=';
    public $app_secret='h:app_secret:id=';
    //用户申请首页
    public function index(Request $request)
    {
        return view('month.index');
    }

    /**
     * 接受用户申请数据
     * @param Request $request
     */
    public function user_info(Request $request)
    {
       /*//文件上传
         $filename = $request->file('user_img');
        $clientName=$filename->getClientOriginalName();
        $tmpName = $filename ->getFileName();
        $realPath = $filename -> getRealPath();
        $entension = $filename -> getClientOriginalExtension();
        $mimeTye = $filename -> getMimeType();
        $newName = $newName = md5(date('ymdhis').$clientName).".". $entension;
        $path = $filename -> move('storage/uploads',$newName);
        */
        //接受用户请求
        $user_name = $request->input('user_name');
        $user_num= $request->input('user_num');
        $user_content = $request->input('user_content');

        //根据用户身份证号码查询是否已存在，存在将原有数据删除
        $where=[
          'u_num'=>$user_num
        ];
        $userInfo=MonthUserModel::where($where)->first();
        if(!empty($userInfo)){
            //删除原有数据
            $del_where=[
              'id'=>$userInfo->id
            ];
            $del_res = MonthUserModel::where($del_where)->delete();
        }
        $data=[
            'u_name'=>$user_name,
            'u_num'=>$user_num,
//            'num_img'=>'/storage/uploads/'.$newName,
            'u_content'=>$user_content
        ];
        $res = MonthUserModel::insertGetId($data);
        if($res){
            echo '申请成功！等待审核';
            header("refresh:2; url='/month/index'");
        }
    }

    /**
     * 后台审核---显示列表
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function audit(Request $request)
    {
        $user_data = MonthUserModel::all()->toArray();
        $info=[
          'data'=>$user_data
        ];
        return view('month.audit',$info);
    }

    /**
     * 后台审核---不通过审核
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function audit_no(Request $request)
    {
        $id = $request->input('id');
        $info=[
          'data'=>$id
        ];
        return view('month.audit_no',$info);
    }
    public function audit_no_do(Request $request)
    {
        //接受拒绝理由和用户id
        $id = $request->input('id');
        $no_content = $request->input('no_content');
        $where=[
          'id'=>$id
        ];
        $data=[
          'status'=>'拒接:'.$no_content
        ];
        //修改申请状态
        $res = MonthUserModel::where($where)->update($data);
        if($res){
            echo '操作成功,跳转中！！！';
            header("refresh:2; url='/month/audit'");
        }
    }

    /**
     * 后台审核---通过审核
     * @param Request $request
     */
    public function audit_do(Request $request)
    {
        //接受用户id
        $id = $request->input('id');
        //修改数据库申请状态
        $where=[
          'id'=>$id
        ];
        $data=[
          'status'=>1
        ];
        $res=MonthUserModel::where($where)->update($data);
        if(!$res){
            echo '操作失败！';die;
        }
        //生成app_key和app_secret
        $app_key=$this->create_key($id);
        $app_secret=$this->create_secret($app_key);
        //存储到redis和数据库
        $key_data=[
          'status'=>'审核通过:app_key='.$app_key.'&app_secret='.$app_secret
        ];
        $res2=MonthUserModel::where($where)->update($key_data);
        $key1=$this->app_key.$id;
        $key2=$this->app_secret.$id;
        $res3=Redis::hset($key1,'app_key',$app_key);
        $res4=Redis::hset($key2,'app_secret',$app_secret);
        if($res2&&$res3&&$res4){
            echo '操作成功';
            header("refresh:2; url='/month/audit'");
        }
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

    /**
     * 接口测试
     * @param Request $request
     */
    public function testAPI(Request $request)
    {

        //获取app_key
        $app_key = $request->input('app_key');
        $app_secret = $this->create_secret($app_key);
//        echo $app_key;echo '</br>';
//        echo $app_secret;die;
        //获取app_secret
        $id = $request->input('id');
        $key1=$this->app_key.$id;
        $key2=$this->app_secret.$id;
        //验证
        $redis_app_secret=Redis::hget($key2,'app_secret');
//        echo $redis_app_secret;die;
        if($app_secret!==$redis_app_secret){
            echo '身份验证失败';die;
        }
        echo 'success';
    }
}
