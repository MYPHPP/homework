<?php
namespace app\bg\controller;

use app\model\Menu;
use app\model\User;
use think\App;
use think\Controller;
use think\Request;

class Background extends Controller
{
    protected $userinfo;
    public function __construct(App $app ,Request $request)
    {
        parent::__construct($app);
        if(!empty(cookie('user'))){
            session('user',cookie('user'));
        }
        if(empty(session('user'))){
            return $this->redirect(url('bg/login/index'));
        }
        $this->userinfo = User::get(session('user'));
        $this->assign('userinfo',$this->userinfo);
        $menuModel = new Menu();
        $route = strtolower($request->module().'/'.$request->controller().'/'.$request->action());
        $route = str_replace('/','\/',$route);
        $this->assign('navMenu',$menuModel->getCategory(explode(',',$this->userinfo->role->access),$route));
    }
}