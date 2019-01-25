<?php
namespace app\bs\controller;

use app\common\model\User;
use think\Controller;
use think\Request;
use app\common\model\Menu;
use tools\GeetestLib;

class Index extends Controller{
    /*
     * 不存在的路由的处理
     * */
    public function _empty(){
        cookie("ms_currentUrl",null);
        return view("error/404");
    }

    /*
     * 权限页面错误跳转
     * */
    public function abort($type = 404){
        cookie("ms_currentUrl",null);
        return $this->fetch('/'.$type);
    }

    /*
     * 登录
     * */
    public function index(Request $request){
        $model = new User;
        if($model->checkLogin()){
            if($this->loginJump()){
                return redirect($this->loginJump());
            }else{
                return $this->fetch('./404');
            }
        }
        if($request->isPost()){
            //验证码验证
            if(!$this->check_geetest($request)){
                $this->error('请先进行验证操作!');
            }
            if(empty($request->name) || empty($request->passwd)){
                $this->error("信息填写不完整");
            }
            $request->name = filterChar($request->name);
            $user = $model->where(['name'=>$request->name])->field('id,name,passwd')->find();
            if(!empty($user)){
                if($user->passwd['change'] != $request->passwd){
                    $this->error('密码错误!');
                }
                session('login_id',$user->id);
                session('login_pwd',$user->passwd['original']);
                if(!empty($request->remember)){
                    cookie("ms_login_id",$user->id,7*86400);
                    cookie("ms_login_pwd",$user->passwd['original'],7*86400);
                }
                $user->lastlogin = $user->update_time;
                $user->ip = $request->ip();
                $user->locktime = null;
                $user->save();
                if(!empty(cookie("ms_currentUrl"))){
                    $this->success('登录成功',cookie("ms_currentUrl"));
                }else{
                    $this->success('登录成功',$this->loginJump());
                }
            }else{
                $this->error('用户不存在!');
            }
        }
        return $this->fetch('login');
    }

    /*
     * 退出
     * */
    public function logout(){
        session(null);
        cookie(null,"ms_");
        return redirect('bs/index/index');
    }

    /*
     * 登录成功页面跳转
     * */
    public function loginJump(){;
        if(empty(cookie('ms_currentUrl'))){
            $menus = Menu::getUserMenu("route");
            if(!empty($menus) && $menus->count() > 0){
                $url = $this->chooseUrl($menus);
            }else{
                $url = false;
            }
        }else{
            $url = cookie('ms_currentUrl');
        }
        return $url;
    }

    public function chooseUrl($route){
        if(!empty($route) && $route->count() > 0){
            foreach($route as $r){
                if(!empty($r->route)){
                    return $r->route;
                }
            }
        }
        return '';
    }

    public function setPageRow(Request $request){
        if($request->isAjax()){
            $num = $request->param('row');
            $num = intval($num);
            if(!in_array($num,Config('setting.page'))){
                $num = 10;
            }
            cookie('pr_pagerow',$num);
        }
    }

    //使用前验证
    public function get_geetest_status(Request $request)
    {
        $geetest = new GeetestLib(config('setting.geetest.id'), config('setting.geetest.key'));
        $data    = array(
            "user_id"     => "0", # 网站用户id
            "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address"  => $request->ip() # 请在此处传输用户请求验证时所携带的IP
        );
        $status = $geetest->pre_process($data, 1);
        session('gtserver',$status);
        session('gt_user_id',$data['user_id']);
        return json($geetest->get_response_str());
    }

    protected function check_geetest(Request $request)
    {
        $return = false;
        $geetest = new GeetestLib(config('setting.geetest.id'), config('setting.geetest.key'));
        $data    = array(
            "user_id"     => session('gt_user_id'), # 网站用户id
            "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address"  => $request->ip()
        );
        if (session('gtserver') == 1) {   //服务器正常
            $result = $geetest->success_validate($request->param('geetest_challenge'), $request->param('geetest_validate'), $request->param('geetest_seccode'), $data);
            if ($result) {
                $return = true;
            }
        } else {  //服务器宕机,走failback模式
            if ($geetest->fail_validate($request->param('geetest_challenge'), $request->param('geetest_validate'))) {
                $return = true;
            }
        }
        return $return;
    }
}
