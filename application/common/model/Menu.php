<?php
namespace app\common\model;

use think\Model;

class Menu extends Model{
    protected $pk = "id";//设置主键

    public function getCF($menu='default'){
        return [
            'pagesize' => [10,20,30],//设置页面每页显示条数
            'default' => [

            ],
        ];
    }
}