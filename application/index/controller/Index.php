<?php
namespace app\index\controller;

use \app\index\Module;
use think\Db;
use think\Request;

class Index extends Module
{
    public function index(Request $request)
    {
        //echo md5(md5('123456'));die;
        return view("index");
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function getCode($data,$key="")
    {
        ksort($data);
        $str = implode('&', array_map(function($v, $k) {
            return $k.'='.$v;}, $data, array_keys($data)));

        $str .= '&key='.$key;
        $code = md5($str);
        return $code;
    }

    public function hello($name = 'ThinkPHP5')
    {
        echo \think\facade\App::version();die;
        return 'hello,' . $name;
    }

    public function redis(){
        $redis = new \Redis();
        $redis->connect("127.0.0.1",6379);
        $redis->set('test',"test word");
        echo $redis->get('test');
    }

    public function test(){
        $mcg = ['北京市','天津市','上海市','重庆市'];
        $ar = ['内蒙古自治区','广西壮族自治区','西藏自治区','宁夏回族自治区','新疆维吾尔自治区'];
        $content = file_get_contents('./aa.csv');
        $arrs = explode(PHP_EOL,$content);
        $addData = [];
        foreach($arrs as $k=>$arr){
            $local = explode(',',$arr);
            $reg1 = '/^('.implode('|',$mcg).')[\x{4e00}-\x{9fa5}]*$/u';
            $reg2 = '/^('.implode('|',$ar).')[\x{4e00}-\x{9fa5}]*$/u';
            if(preg_match($reg1,$local[1],$matches)){
                $province = '';
                $city = mb_substr($local[1],0,3);
                $area = '';
                if(strlen($local[1]) > 9){
                    $area = mb_substr($local[1],3);
                }
            }else if(preg_match($reg2,$local[1],$matches)){
                $province = $matches[1];
                $remaining = str_replace($province,'',$local[1]);
                echo $remaining.'<br/>';
            }
//            $province = '';
//            $city = '';
//            $area = '';
//            $addData[$k]['province'] = $province;
//            $addData[$k]['city'] = $city;
//            $addData[$k]['area'] = $area;
//            $addData[$k]['address'] = $local[1];
        }
        dd($addData);
    }
}
