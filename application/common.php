<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/*
 * 输出
 * */
function dd($data= ''){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}

/*
 * 数组转对象
 * @param $arr array
 * return obj
 * */
function array2obj($arr){
    return json_decode(json_encode($arr));
}

/*
 * 过滤字符
 * */
function filterChar($val){
    if(!empty($val)){
        if(!get_magic_quotes_gpc()){
            $val = addslashes($val);
            if(is_numeric($val)){
                $val = strval($val);
            }
        }
    }
    return $val;
}