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
                    'unique' => true
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
                'mobile' => [
                    'label' => '手机号',
                    'type' => 'text',
                    'placeholder' => '请填手机号',
                    'validate' => [
                        'rule' => 'require|length:11|regex:^1[3,5,6,7,8,9]\d{9}',
                        'mobile.require' => "手机号不能为空",
                        'mobile.length' => "手机号要为11位的数字",
                        'mobile.regex' => "手机号格式错误",
                    ],
                    'unique' => true
                ],
            ],
        ];
    }
}