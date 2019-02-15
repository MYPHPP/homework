<?php
namespace app\api\controller;

use app\api\model\User;
use app\api\validate\User as ValidateUser;
use think\Controller;
use think\facade\Cache;
use think\Request;

class Login extends Controller
{
    public function login(Request $request){
        $data = $request->param();
        $validate = new ValidateUser;
        if(!$validate->check($data)) return json(['code'=>10008,'msg'=>$validate->getError()]);
        $error_nums = Cache::store('redis')->get($data['username'].'_error');
        if($error_nums !== false && $error_nums == 0) return json(['code'=>10008,'msg'=>'今天密码错误次数已达上限,请明日再试或重置密码']);
        $model = new User();
        $user = $model->where('name',$data['username'])->find();
        if(!$user) return json(['code'=>10008,'msg'=>'用户不存在']);
        if($user->passwd['change'] != $data['password']){
            if(Cache::store('redis')->get($data['username'].'_error')){
                Cache::store('redis')->dec($data['username'].'_error');
            }else{
                $expiretime = strtotime('+1 day',strtotime(date('Y-m-d')))-time();
                Cache::store('redis')->set($data['username'].'_error',4,$expiretime);
            }
            $try = Cache::store('redis')->get($data['username'].'_error');
            if($try == 0){
                return json(['code'=>10008,'msg'=>'今天密码错误次数已达上限,请明日再试或重置密码']);
            }
            return json(['code'=>10008,'msg'=>'密码错误,还剩'.$try.'次机会']);
        }
        return json(['code'=>200,'msg'=>$model->createToken($user->id)]);
    }
}