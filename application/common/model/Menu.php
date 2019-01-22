<?php
namespace app\common\model;

use app\common\validate\Menu as validateMenu;

class Menu extends Base {

    /**
     * @param $value
     * @return string
     */
    public function setDescriptionAttr($value){
        return htmlentities($value);
    }

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
            return $model->order('sort',"desc")->select();
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
    public function getCategory($access ,$pids ,$current=1 ,$pid=0 ,$html=''){
        $menus = $this->where('position',1)
            ->where('pid',$pid)
            ->whereIn('id',$access)
            ->order('sort asc')
            ->select();
        if($menus->count() > 0){
            if($pid == 0){
                $html .= '<ul class="sidebar-menu" data-widget="tree"><li class="header">目录列表</li>';
            }else{
                $html .= '<ul class="treeview-menu">';
            }
            foreach($menus as $menu){
                $childs = $this->where('pid',$menu->id)->where('position','=',1)->count();
                $treeview = '';
                if($childs > 0){
                    $treeview = 'treeview';
                }
                if(in_array($menu->id,$pids) || $menu->id == $current){
                    $treeview = $treeview.' active';
                }
                $html .='<li class="'.$treeview.'">';
                if(!empty($menu->route)){
                    $html .= '<a href="'.url($menu->route).'">';
                }else{
                    $html .= '<a href="javascript:;">';
                }
                $html .= '<i class="fa '.$menu->icon.'"></i>';
                if($pid == 0){
                    $html .= '<span>'.$menu->title.'</span>';
                }else{
                    $html .= $menu->title;
                }
                if($childs > 0){
                    $html .= '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
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
                $icon = !empty($menu['icon']) && $key == 0 ? '<i class="fa '.$menu['icon'].'"></i>' : "";
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

    /*
     * 获取目录
     * */
    public function getMenu($pid=0,$arr=array(),$level=0){
        $menus = $this->where("pid",$pid)->field("id,title")->order('sort')->select();
         if($menus->count() > 0){
             foreach ($menus as $k=>$v){
                 if(!empty($v->title)){
                     $prefix = '';
                     for($i=1;$i<=$level;$i++){
                         $prefix .= "|- ";
                     }
                     $v->title = $prefix.$v->title;
                     $arr[] = $v;
                     $arr = $this->getMenu($v->id,$arr,$level+1);
                 }
             }
         }
         return $arr;
    }

    /**
     * 根据路由判断是否有权限
     * @param $url
     * @param $site
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUrl($url ,$site=1){
        $res = false;
        $url = trim($url);
        if(!empty($url)){
            $url = url($url);
            $url = str_replace('.html','',$url);
            $url = ltrim($url,'/');
            $menu = $this->where('position','=',$site)->where('route','like',$url.'%')->find();
            $role = User::where('id',session('login_id'))->find();
            if(!empty($role->role->access) && !empty($menu)){
                $access = explode(',',$role->role->access);
                if(in_array($menu->id,$access)){
                    $res = true;
                }
            }
        }
        return $res;
    }

    public function validateData($data){
        $return = ['status'=>true];
        $model = new validateMenu();
        if(!$model->batch()->check($data)){
            $return['status'] = false;
            $return['msg'] = implode(' | ',$model->getError());
        }
        return $return;
    }

    public function getShowList(){
        $data = [];
        $pmenu = $this->where('pid','=',0)->select();
        if(!empty($pmenu)){
            foreach($pmenu as $menu){
                $menu->parentTitle = '一级目录';
                $data[] = $menu;
                $arr = $this->getChildMenu($menu->id ,$menu->title);
                $data = array_merge($data,$arr);
            }
        }
        return $data;
    }

    public function getChildMenu($pid ,$title ,$arr=[]){
        $menu = $this->where('pid','=',$pid)->order('sort asc')->select();
        if($menu->count()){
            foreach($menu as $m){
                $m->parentTitle = $title;
                $arr[] = $m;
                $arr = $this->getChildMenu($m->id ,$m->title ,$arr);
            }
        }
        return $arr;
    }
}