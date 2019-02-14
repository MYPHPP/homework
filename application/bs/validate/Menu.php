<?php

namespace app\bs\validate;

use think\Validate;

class Menu extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'position' => 'require',
        'title' => 'require',
        'sort' => 'number',
        'route' => 'checkRoute'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'position.require' => '请选择菜单位置',
        'title.require' => '请填写标题',
        'sort.number' => '排序值为正整数',
    ];

    // 自定义验证规则
    protected function checkRoute($value,$rule)
    {
        $res = true;
        if(!empty($value)){
            $rules = explode('/',$value);
            if(count($rules) < 3) return '路由格式错误';
        }
        return $res;
    }
}
