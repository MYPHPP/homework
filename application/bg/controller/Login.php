<?php
namespace app\bg\controller;

use app\model\User;
use think\Controller;
use think\captcha\Captcha;
use think\Request;
use think\Validate;
use think\Config;

class Login extends Controller{
    public function index(Request $request){
        if($request->isAjax()){
            $data = array_map(function($v){$v = trim($v);return $v;},$request->Post());
            $rule = [
                'username' => 'require|min:3|max:8',
                'password' => 'require',
                'code' => 'require',
            ];
            $msg = [
                'username.require' => '用户名不能为空',
                'username.min' => '用户名最少3个字符长度',
                'username.max' => '用户名最多8个字符长度',
                'password.require' => '密码不能为空',
                'code.require' => '验证码不能为空',
            ];
            $validate = Validate::make($rule,$msg);
            if(!$validate->check($data)){
                exit(json_encode(['status'=>0,'msg'=>$validate->getError()]));
            }
            if(!captcha_check($data['code'])){
                exit(json_encode(['status'=>0,'msg'=>'验证码错误']));
            }
            if(!empty($user)){
                exit(json_encode(['status'=>1,'msg'=>'跳转']));
            }else{
                exit(json_encode(['status'=>0,'msg'=>'用户名或密码错误']));
            }
        }
        return view();
    }

    public function register(){
        echo 1;
    }

    public function createVerify(){
        $captcha = new Captcha();
        return $captcha->entry();
    }
}