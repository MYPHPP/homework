<?php
namespace app\bg\controller;

use app\model\Menu;
use app\model\User;
use think\App;
use think\Controller;
use think\Db;
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
        $route = strtolower($request->module().'/'.$request->controller().'/'.$request->action());
        $this->checkRole($route);
    }

    public function checkRole($route){
        $pids = [];
        $access = explode(',',$this->userinfo->role->access);
        $menuModel = new Menu();
        $menus = $menuModel->where('is_del',0)->whereIn('id',$access)->field('id,route')->select();
        if($menus->count() > 0){
            foreach($menus as $menu){
                if(!empty($menu->route)){
                    $mArr = explode('/',$menu->route);
                    if(count($mArr)>2){
                        if($route == strtolower($mArr[0].'/'.$mArr[1].'/'.$mArr[2])){
                            $pids = $menuModel->getPids($menu->id);
                        }
                    }
                }
            }
        }
        $this->assign('navMenu',$menuModel->getCategory($access,$pids));
        if(empty($pids)){
            //return $this->redirect(url('bg/error/show'));
            return $this->errorPage();
        }
    }

    public function errorPage(){
        return view('error/404');
    }

    public function _empty(){
        return view('error/404');
    }
}