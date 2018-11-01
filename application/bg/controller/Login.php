<?php
namespace app\bg\controller;

use app\model\Menu;
use app\model\User;
use think\Controller;
use think\captcha\Captcha;
use think\Request;
use think\Validate;

class Login extends Controller{
    public function index(Request $request){
        if(!empty(cookie('user')) || !empty(session('user'))){
            $userinfo = User::find(session('user'));
            $menuModel = new Menu();
            $homePage = $menuModel->getHomePage(explode(',',$userinfo->role->access));
            return redirect(url($homePage));
        }
        $isCheckVerify = User::VERIFYCODE;
        if($request->isAjax()){
            $data = array_map(function($v){$v = trim($v);return $v;},$request->Post());
            $rule = [
                'username' => 'require|min:3|max:8',
                'password' => 'require',
            ];
            $msg = [
                'username.require' => '用户名不能为空',
                'username.min' => '用户名最少3个字符长度',
                'username.max' => '用户名最多8个字符长度',
                'password.require' => '密码不能为空',
            ];
            if($isCheckVerify){
                $rule['code'] = 'require';
                $msg['code.require'] = '验证码不能为空';
            }
            $validate = Validate::make($rule,$msg);
            if(!$validate->check($data)){
                exit(json_encode(['status'=>0,'msg'=>$validate->getError()]));
            }
            if($isCheckVerify){
                if(!captcha_check($data['code'])){
                    exit(json_encode(['status'=>0,'msg'=>'验证码错误']));
                }
            }
            $userModel = new User();
            $userinfo = $userModel->checkLogin($data['username'],$data['password']);
            if(!$userinfo){
                exit(json_encode(['status'=>0,'msg'=>'用户名或密码错误']));
            }else{
                $menuModel = new Menu();
                $homePage = $menuModel->getHomePage(explode(',',$userinfo->role->access));
                if(!empty($homePage)){
                    if(!empty($request->remember) && $request->remember == 1){
                        cookie('user',$userinfo->id,3600*8);
                    }
                    session('user',$userinfo->id);
                    exit(json_encode(['status'=>1,'msg'=>url($homePage)]));
                }else{
                    exit(json_encode(['status'=>0,'msg'=>'该账号没有任何权限,联系管理员处理']));
                }
            }
        }
        $this->assign('isShow',$isCheckVerify);
        return view();
    }

    public function register(){
        echo 1;
    }

    public function createVerify(){
        $captcha = new Captcha();
        return $captcha->entry();
    }

    public function logout(){
        session('user',null);
        cookie('user',null);
        return redirect(url('bg/login/index'));
    }
}