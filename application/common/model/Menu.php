<?php
namespace app\common\model;

class Menu extends Base {

    /*
     *登录用户权限菜单
     * */
    public static function getUserMenu($fields = []){
        $role = User::where('id',session('login_id'))->find();
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
    public static function getLogoLink(){
        $route = '';
        $menus = self::getUserMenu('route')->toArray();
        $menus = array_column($menus,'route');
        foreach ($menus as $menu){
            $marr = explode("/",$menu);
            if(count($marr) > 2){
                current($marr);
                next($marr);
                if(strtolower(next($marr)) == "index"){
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

    /*
     * 查找某个菜单的所有父级菜单ID
     * */
    public function getPids($id,$pids=[]){
        $pid = $this->where('id',$id)->value('pid');
        if(isset($pid) && $pid != 0){
            $pids[] = $pid;
            $pids = $this->getPids($pid,$pids);
        }
        return $pids;
    }

    /**
     * Description 左侧菜单栏
     * @CreateTime 2018/11/1 9:40:10
     * @param $access
     * @param $pids
     * @param int $pid
     * @param string $html
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategory($access ,$pids ,$current=1 ,$pid=0 ,$html='')
    {
        $menus = $this->where('position',1)
            ->where('pid',$pid)
            ->whereIn('id',$access)
            ->order('sort desc')
            ->select();
        if($menus->count() > 0){
            if($pid == 0){
                $html .= '<ul class="page-sidebar-menu"><li><div class="sidebar-toggler hidden-phone"></div></li>';
            }else{
                $html .= '<ul class="sub-menu">';
            }
            foreach($menus as $menu){
                if(in_array($menu->id,$pids) || $menu->id == $current){
                    $html .= '<li class="active">';
                }else{
                    $html .= '<li class="">';
                }
                if(!empty($menu->route)){
                    $html .= '<a href="'.url($menu->route).'">';
                }else{
                    $html .= '<a href="javascript:;">';
                }
                $html .= '<i class="'.$menu->icon.'"></i><span class="title">'.$menu->title.'</span>';
                $childs = $this->where('pid',$menu->id)->where('position','=',1)->count();
                if($childs > 0){
                    $html .= '<span class="arrow "></span>';
                }
                $html .='</a>';
                $html = $this->getCategory($access ,$pids ,$current ,$menu->id ,$html);
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /*
     * 左侧菜单栏(暂时废弃)
     * */
    public function getNav($ids,$pidArr,$visit,$pid=0,$level=0){
        $html = '';
        $menus = Menu::whereIn('id',$ids)->where('pid',$pid)->where("position",1)->order("sort")->select()->toArray();
        if(!empty($menus)){
            foreach($menus as $v){
                $route = !empty($v['route']) ? url($v['route']) : "javascript:;";
                $icon = isset($v['icon']) ? '<i class="'.$v['icon'].'"></i>' : '';
                $routeArr = explode('/',strtolower($v['route']));
                $menuRoute = current($routeArr)."/".next($routeArr)."/".next($routeArr);
                $active = in_array($v['id'],$pidArr) || $menuRoute == $visit  ? "active" : '';
                //$open = in_array($v['id'],$pidArr) ? "open" : '';
                $open = $active == "active" ? "arrow open" : 'arrow ';
                $left = empty($v['route']) || $this->where('pid',$v['id'])->where("position",1)->select()->count() > 0 ? '<span class="'.$open.' "></span>' : '<span class="selected">';
                $html .= '<li class="'.$active.'"><a href="'.$route.'">'.$icon.'<sapn class="title">'.$v['title'].'</sapn>'.$left.'</a>';
                $html .= $this->getNav($ids,$pidArr,$visit,$v['id'],$level+1);
                $html .= "</li>";
            }
        }
        if(!empty($html)){
            if($level == 0){
                $html = '<ul class="page-sidebar-menu"><li><div class="sidebar-toggler hidden-phone"></div></li>'.$html."</ul>";
            }else{
                $html = '<ul class="sub-menu">'.$html."</ul>";
            }
        }
        return $html;
    }

    /*
     * 查找所有菜单
     * */
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

    /*
     * 内容导航栏
     * */
    public function getTab($pids,$menuid){
        $html = '';
        $h3 = '';
        $currentMenu = $this->where('id',$menuid)->field('route,title,icon')->find();
        if(!empty($pids)){
            sort($pids);
            foreach($pids as $key=>$pid){
                $menu = $this->where('id',$pid)->field('route,icon,title')->find();
                if($key == 0){
                    $h3 = $menu['title'];
                }
                $childs = $this->where('pid',$pid)->select()->count();
                $icon = !empty($menu['icon']) && $key == 0 ? '<i class="'.$menu['icon'].'"></i>' : "";
                $right = $childs > 0 ? '<i class="icon-angle-right"></i>' : "";
                $href = !empty($menu['route']) ? url($menu['route']) : "javascript:;";
                $html .= '<li>'.$icon.'<a href="'.$href.'">'.$menu['title'].'</a>'.$right.'</li>';
            }
            $html .= '<li><a href="'.url($currentMenu['route']).'">'.$currentMenu['title'].'</a></li>';
        }else{
            $h3 = $currentMenu['title'];
            $icon = !empty($currentMenu['icon']) ? '<i class="'.$currentMenu['icon'].'"></i>' : "";
            $html .= '<li>'.$icon.'<a href="'.url($currentMenu['route']).'">'.$currentMenu['title'].'</a></li>';
        }
        return ['html'=>$html,'h3'=>$h3];
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
                 if(!empty($v['title'])){
                     $prefix = '';
                     for($i=1;$i<=$level;$i++){
                         $prefix .= "|-";
                     }
                     $v['title'] = $prefix.$v['title'];
                     $arr[] = $v;
                     $arr = $this->getMenu($v['id'],$arr,$level+1);
                 }
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
                    'tip' => "路由地址格式：模块/控制器/方法，例：bs/index/index",
                    'advance' =>true
                ],
                "position" => [
                    "label" => "展示位置",
                    "type" => "select",
                    'placeholder' => '请选择展示位置',
                    "required" => true,
                    "data" => config("setting.menu.position")
                ],
                "icon" => [
                    "label" => "图标",
                    "type" => "select3",
                    "data" => config("setting.menu.icon")
                ]
            ],
        ];
    }
}