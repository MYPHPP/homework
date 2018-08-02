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
                ],
                "icon" => [
                    "label" => "图标",
                    "type" => "select2",
                    "data" => config("setting.menu.icon")
                ]
            ],
        ];
    }
}