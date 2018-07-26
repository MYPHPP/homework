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
    public function getMenuByIds($ids,$pid=0){
        $menus = Menu::whereIn('id',$ids)->where('pid',$pid)->select()->toArray();
        if(!empty($menus)){
            foreach($menus as $k=>$v){
                $child = $this->getMenuByIds($v['id']);
                if(!empty($child)){
                    $menus[$k]['child'] = $child;
                }
            }
        }
        return $menus;
    }

    public function build_tree($ids,$root_id=0){
        $childs=Menu::whereIn('id',$ids)->where('pid',$root_id)->select()->toArray();
        if(!empty($childs)){
            foreach ($childs as $k => $v){
                $rescurTree=$this->build_tree($ids,$v['id']);
                if( null != $rescurTree){
                    $childs[$k]['childs']=$rescurTree;
                }
            }
        }
        return $childs;
    }

    public function setDescriptionAttr($value){
        return htmlentities($value);
    }

    /*
     * 获取目录
     * */
    protected function getMenu($pid=0,$arr=array(),$level=0){
        $menus = $this->where("pid",$pid)->field("id,title")->select();
         if(!empty($menus)){
             foreach ($menus as $k=>$v){
                 $prefix = '';
                 for($i=1;$i<=$level;$i++){
                     $prefix .= "|-";
                 }
                 $v->title = $prefix.$v->title;
                 $arr[] = $v;
                 $arr = $this->getMenu($v->id,$arr,$level+1);
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
                    "data" => $this->getMenu()
                ],
                "sort" => [
                    "label" => "单选",
                    "type" => 'radio',
                    "required" => true,
                    "data" => [0=>["label"=>'男',"checked"=>true],1=>["label"=>"女"]]
                ],
                'route' => [
                    "label" => "路由地址",
                    "type" => "text",
                    'placeholder' => '请填写路由',
                    'tip' => "路由地址格式：模块/控制器/方法，例：bs/index/index"
                ],
            ],
        ];
    }
}