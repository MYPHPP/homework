<?php
namespace app\bg\controller;

use think\Controller;
use think\captcha\Captcha;
use think\Request;
use think\Validate;

class Login extends Controller{
    public function index(Request $request){
        if($request->isAjax()){
            $rule = [
                'username' => 'required,min:3|max:8',
                'password' => 'required',
                'code' => 'required',
            ];
            
            dd($request->Post());
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