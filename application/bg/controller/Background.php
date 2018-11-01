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
        $access = explode(',',$this->userinfo->role->access);
        $menuModel = new Menu();
        $this->assign('hompage',url($menuModel->getHomePage($access)));
        $route = strtolower($request->module().DIRECTORY_SEPARATOR.$request->controller().DIRECTORY_SEPARATOR.$request->action());
        if(!$this->checkRole($menuModel ,$access ,$route)){
            return view('error/404');
        }
    }

    /**
     * Description 判断角色对路由的权限
     * @CreateTime 2018/11/1 14:26:33
     * @param $menuModel
     * @param $access
     * @param $route
     * @return bool
     */
    public function checkRole($menuModel ,$access ,$route){
        $pids = [];
        $menus = $menuModel->where('is_del',0)->whereIn('id',$access)->field('id,route')->select();
        if($menus->count() > 0){
            foreach($menus as $menu){
                if(!empty($menu->route)){
                    $mArr = explode('/',$menu->route);
                    if(count($mArr)>2){
                        if($route == strtolower($mArr[0].DIRECTORY_SEPARATOR.$mArr[1].DIRECTORY_SEPARATOR.$mArr[2])){
                            $pids = $menuModel->getPids($menu->id);
                        }
                    }
                }
            }
        }
        $this->assign('navMenu',$menuModel->getCategory($access,$pids));
        if(empty($pids)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Description 空操作跳转
     * @CreateTime 2018/11/1 14:26:51
     * @return \think\response\View
     */
    public function _empty(){
        return view('error/404');
    }
}