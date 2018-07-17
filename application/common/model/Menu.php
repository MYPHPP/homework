<?php
namespace app\common\model;

class Menu extends Base {
    public function setDescriptionAttr($value){
        return htmlentities($value);
    }

    /*
     * 获取上级目录
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
                'route' => [
                    "label" => "路由",
                    "type" => "text",
                    'placeholder' => '请填写路由',
                    "size" => 3
                ],
                "title" => [
                    "label" => "名称",
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
                "radio" => [
                    "label" => "单选",
                    "type" => 'radio',
                    "required" => true,
                    "data" => [0=>["label"=>'男',"checked"=>true],1=>["label"=>"女"]]
                ]
            ],
        ];
    }
}