<?php
namespace app\model;

class Menu extends Common
{
    protected $pk = 'id';

    public function getCategory($access ,$pids ,$pid=0 ,$html=''){
        if(is_array($access)) {
            $menus = $this->where('is_del',0)->where('display',1)->where('pid',$pid)->whereIn('id',$access)->select();
            if($menus->count() > 0){
                if($pid == 0){
                    $html .= '<ul class="page-sidebar-menu"><li><div class="sidebar-toggler hidden-phone"></div></li>';
                }else{
                    $html .= '<ul class="sub-menu">';
                }
                foreach($menus as $menu){
                    if(in_array($menu->id,$pids)){
                        $html .= '<li class="active">';
                    }else{
                        $html .= '<li class="">';
                    }
                    if(!empty($menu->route)){
                        $html .= '<a href="'.url($menu->route).'">';
                    }else{
                        $html .= '<a href="javascript:;">';
                    }
                    $html .= '<i class="'.$menu->icon.'"></i><span class="title">'.$menu->route_name.'</span>';
                    $childs = $this->where('pid',$menu->id)->count();
                    if($childs > 0){
                        $html .= '<span class="arrow "></span>';
                    }
                    $html .='</a>';
                    $html = $this->getCategory($access ,$pids ,$menu->id ,$html);
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        }
        return $html;
    }

    public function getPids($id ,$pids=array()){
        $pids[] = $id;
        $pid = $this->where('id',$id)->field('pid')->find();
        if(!empty($pid) && $pid->pid > 0){
            $pids = $this->getPids($pid->pid,$pids);
        }
        return $pids;
    }
}