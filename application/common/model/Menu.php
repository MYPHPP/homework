<?php
namespace app\common\model;

class Menu extends Base {


    /*
     *登录用户权限菜单
     * */
    static public function getUserMenu($fields = []){
        $role = User::where('lid',session('login_id'))->find();
        if(!empty($role) && !empty($role->role->access)){
            $model = new Menu();
            $model = $model->whereIn("id",$role->role->access);
            if(!empty($fields)){
                if(is_array($fields)){
                    $model = $model->field(explode(',',$fields));
                }
                if(is_string($fields)){
                    $model = $model->field($fields);
                }
            }
            return $model->order('sort',"asc")->select();
        }
        return null;
    }

    /*
     * 后台logo对应的链接
     * */
    static public function getLogoLink(){
        $route = '';
        $menus = self::getUserMenu('route')->toArray();
        $menus = array_column($menus,'route');
        if(in_array("bs/common/dashboard",$menus)){
            $route = "bs/common/dashboard";
        }else{
            foreach ($menus as $menu){
                $num = strpos($menu,"index");
                if(isset($num)){
                    $route = $menu;
                    break;
                }
            }
        }
        return $route;
    }

    /*
     * 根据id获取相应的菜单
     * */
    public function getMenuById($ids,$pid=0,$level=2){
        if($level > 0){
            $menus = Menu::whereIn('id',$ids)->where('pid',$pid)->where("position",1)->order("sort")->select()->toArray();
            if(!empty($menus)){
                foreach($menus as $k=>$v){
                    $child = $this->getMenuByIds($ids,$v['id'],$level-1);
                    if(!empty($child)){
                        $menus[$k]['child'] = $child;
                    }
                }
            }
            return $menus;
        }
    }

    public function getNav($ids,$pid=0,$level=0){
        $html = '';
        $menus = Menu::whereIn('id',$ids)->where('pid',$pid)->where("position",1)->order("sort")->select()->toArray();
        if(!empty($menus)){
            foreach($menus as $v){
                $route = !empty($v['route']) ? url($v['route']) : "javascript:;";
                $icon = isset($v['icon']) ? '<i class="'.$v['icon'].'"></i>' : '';
                $left = empty($v['route']) ? '<i class="right fa fa-angle-left"></i>' : '';
                $html .= '<li  class="nav-item has-treeview"><a class="nav-link" href="'.$route.'">'.$icon.'<p>'.$v['title'].$left.'</p></a>';
                $html .= $this->getNav($ids,$v['id'],$level+1);
                $html .= "</li>";
            }
        }
        if(!empty($html)){
            if($level == 0){
                $html = '<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">'.$html."</ul>";
            }else{
                $html = '<ul class="nav nav-treeview">'.$html."</ul>";
            }
        }
        return $html ;
    }

    public function getMenuByIds($menus,$pid=0){
            $html='';
            if(!empty($menus)){
                foreach($menus as $k=>$v){
                    if($v['pid'] == $pid){
                        $html .= "<li>".$v['title'];
                        $html .= $this->getMenuByIds($menus, $v['id']);
                        $html = $html."</li>";
                    }
                }
            }
            return $html ? '<ul>'.$html.'</ul>' : $html ;
    }

    public function setDescriptionAttr($value){
        return htmlentities($value);
    }

    /*
     * 获取目录
     * */
    public function getMenu($pid=0,$arr=array(),$level=0){
        $menus = $this->where("pid",$pid)->field("id,title")->order('sort')->select()->toArray();
         if(!empty($menus)){
             foreach ($menus as $k=>$v){
                 $prefix = '';
                 for($i=1;$i<=$level;$i++){
                     $prefix .= "|-";
                 }
                 $v['title'] = $prefix.$v['title'];
                 $arr[] = $v;
                 $arr = $this->getMenu($v['id'],$arr,$level+1);
             }
         }
         return $arr;
    }

    public function getCF($menu='default'){
        return [
            'pagesize' => [10,20,30],//设置页面每页显示条数
            'default' => [
                "title" => [
                    "label" => "菜单名称",
                    "type" => "text",
                    'placeholder' => '请填写路由',
                    "required" => true,
                    'validate'=>[
                        'rule'=>"require",
                        'title.require' => "名称不能为空",
                    ]
                ],
                'description' => [
                    "label" => "描述",
                    "type" => "textarea",
                    "placeholder" => "填写描述",
                ],
                'pid' => [
                    "label" => "上级菜单",
                    "type" => "select2",
                    "data" => array_column($this->getMenu(),'title','id')
                ],
                "sort" => [
                    "label" => "单选",
                    "type" => 'radio',
                    "required" => true,
                    "data" => config('setting.menu.sex')
                ],
                'route' => [
                    "label" => "路由地址",
                    "type" => "text",
                    'placeholder' => '请填写路由',
                    'tip' => "路由地址格式：模块/控制器/方法，例：bs/index/index"
                ],
                "position" => [
                    "label" => "展示位置",
                    "type" => "select",
                    'placeholder' => '请选择展示位置',
                    "required" => true,
                    "data" => config("setting.menu.position")
                ]
            ],
        ];
    }
}