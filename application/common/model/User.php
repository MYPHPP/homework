<?php
namespace app\common\model;

use think\Model;

class User extends Model{
    protected $pk = "id";//设置主键
    //protected $table = "tt_user";//设置表名
    //protected $connection = "";//设置数据库

    /*
     * 设置角色关联模型
     * */
    public function role(){
        return $this->hasOne('Role',"id",'role_id');
    }

    /*
     * 验证登录
     * */
    public function checkLogin(){
        $check = false;
        if(!empty(cookie("ms_login_id") || !empty(session('login_id')))){
            if(empty(session('login_id'))){
                session('login_id',cookie("ms_login_id"));
                session('login_pwd',cookie("ms_login_pwd"));
            }
            $user = User::get(session('login_id'));
            if($user['passwd'] == session('login_pwd')){
                $check = true;
            }else{
                session('login_id',null);
                session('login_pwd',null);
                cookie("ms_login_id",null);
                cookie("ms_login_pwd",null);
            }
        }
        return $check;
    }

    /*
     * 验证权限
     * */
    public function checkAuth($url){
        $auth = false;
        $menus = Menu::getUserMenu('route');
        if(!empty($menus) && (is_object($menus) && $menus->count() >0)){
            foreach ($menus as $menu){
                $route = explode('/',strtolower($menu->route));
                $menuRoute = current($route)."/".next($route)."/".next($route);
                if($menuRoute == $url){
                    return true;
                }
            }
        }
        return $auth;
    }

    /*
     * 获取登录用户的信息
     * */
    public function getLoginInfo($field=''){
        $model = $this->where($this->pk,session('login_id'));
        if(!empty($field)){
            $model = $model->field($field);
        }
        return $model->find();
    }
}