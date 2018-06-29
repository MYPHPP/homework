<?php
namespace app\common\model;

use think\Model;

class User extends Model{
    protected $pk = "id";//设置主键
    protected $table = "tt_user";//设置表名
    //protected $connection = "";//设置数据库

    /*
     * 配置字段相关信息
     * */
    public function getCF($menu='default'){
        return [
            'pagesize' => [10,20,30],//设置页面每页显示条数
            'default' => [
                'name' => [
                    'label' => '姓名',
                    'type' => "text",
                    'validate'=>[
                        'rule'=>"require|max:5",
                        'name.require' => "姓名不能为空",
                        'name.max'=>'姓名长度不能超过5个字符'
                    ],
                    'placeholder' => '请填写用户名',
                ],
                'passwd' => [
                    'label' => '密码',
                    'type' => 'password',
                    'placeholder' => '请填写密码',
                    'validate' => [
                        'rule' => 'require',
                        'passwd.require' => "密码不能为空",
                    ]
                ],
                'sex' => [
                    'label' => '性别',
                    'type' => 'radio',
                    'data' => ['男','女']
                ],
            ],
        ];
    }
}