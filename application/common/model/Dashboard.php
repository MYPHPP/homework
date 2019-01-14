<?php
namespace app\common\model;

class Dashboard extends Base {
//    protected $table = "tt_menu";
//    protected $pk = "id";

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
}