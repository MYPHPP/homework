<?php
namespace app\common\model;

class Role extends Base {
    public function getCF($menu='default'){
        return [
            'pagesize' => [10,20,30],//设置页面每页显示条数
            'default' => [
                'route' => [
                    "label" => "路由",
                    "type" => "text",
                    'placeholder' => '请填写路由',
                    "size" => 3,
                    'tip' => "路由格式：模块/控制器/方法，例：bs/index/index"
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
                "sort" => [
                    "label" => "单选",
                    "type" => 'radio',
                    "required" => true,
                    "data" => [0=>["label"=>'男',"checked"=>true],1=>["label"=>"女"]]
                ],
            ],
        ];
    }
}