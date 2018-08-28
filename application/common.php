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

function curlSend($url,$data='',$type="get"){
    $type = strtolower($type);
    $result['status'] = 0;
    $result['msg'] = '';
    if(empty($url)){return $result;}
    try{
        $curl = curl_init();//初始化
        curl_setopt($curl, CURLOPT_URL, $url);//设置抓取的url
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 跟踪重定向
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);// 不从证书中检查SSL加密算法是否存在
        //数据量大的时候设置
        //curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //头部要送出'Expect: '
        //curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        if($type != "get"){
            if(empty($data)){
                curl_close($curl);
                $result['msg'] = "请求类型缺少参数";
                return $result;
            }
            curl_setopt($curl, CURLOPT_POST, 1);// 设置请求方式为post
            if($type == "json"){
                $data = json_encode($data);
            }else{
                $data = http_build_query($data);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 提交数据
            if($type == "json"){
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data)
                    )
                );//设置header头
            }
        }
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);//在尝试连接时等待的秒数。设置为0，则无限等待。
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);//设置超时时间
        $res = curl_exec($curl);//执行命令
        $result['status'] = 1;
        $result['msg'] = $res;
        //dd(curl_getinfo($curl));
        curl_close($curl);//关闭句柄
    }catch (Exception $e){
        $result['msg'] = $e->getMessage();
    }
    return $result;
}