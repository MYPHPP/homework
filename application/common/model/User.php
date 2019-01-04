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
        return $this->hasOne('Role',"id",'roleid');
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
        $model = $this->where('lid',session('login_id'));
        if(!empty($field)){
            $model = $model->field($field);
        }
        return $model->find();
    }

    /*
     * 配置字段相关信息
     * */
    public function getCF($menu='default'){
        return [
            'pagesize' => [10,20,30],//设置页面每页显示条数
            'default' => [
                'name' => [
                    'label' => '姓名',
                    'type' => "text",
                    'required'=> true,
                    'validate'=>[
                        'rule'=>"require|max:10|unique:user",
                        'name.require' => "姓名不能为空",
                        'name.max'=>'姓名长度不能超过10个字符',
                        'name.unique'=>'改用户名已被注册',
                    ],
                    'placeholder' => '请填写用户名',
                ],
                'passwd' => [
                    'label' => '密码',
                    'type' => 'password',
                    'required'  => true,
                    'placeholder' => '请填写密码',
                    'validate' => [
                        'rule' => 'require|min:6',
                        'passwd.require' => "密码不能为空",
                        'passwd.min' => "密码最少为6位字符",
                    ]
                ],
                'sex' => [
                    'label' => '性别',
                    'type' => 'radio',
                    'data' => ['男','女']
                ],
                'mobile' => [
                    'label' => '手机号',
                    'type' => 'text',
                    'placeholder' => '请填手机号',
                    'validate' => [
                        'rule' => 'require|length:11|regex:^1[3,5,6,7,8,9]\d{9}',
                        'mobile.require' => "手机号不能为空",
                        'mobile.length' => "手机号要为11位的数字",
                        'mobile.regex' => "手机号格式错误",
                    ],
                    'unique' => true
                ],
                'email' => [
                    'label' => '邮箱',
                    'type' => 'text',
                    'placeholder' => '请填邮箱',
                    'validate' => [
                        'rule' => 'email',
                        'email.email' => "邮箱格式错误",
                    ]
                ]
            ],
        ];
    }
}