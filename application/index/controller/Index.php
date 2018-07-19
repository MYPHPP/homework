<?php
namespace app\index\controller;

use \app\index\Module;
use think\Db;
use think\Request;

class Index extends Module
{
    public function index(Request $request)
    {
        $oids = [];
        $json = file_get_contents("./yqdata20180717.txt");
        $datas = json_decode($json,true);
        foreach ($datas['data'] as $data){
            $oids[] = $data['withdraw']['serial_number'];
        }
        $oids1 = [];
        $json1 = file_get_contents("./yqdata20180718.txt");
        $datas1 = json_decode($json1,true);
        foreach ($datas1['data'] as $data1){
            $oids1[] = $data1['withdraw']['serial_number'];
        }
        $ids = array_diff($oids1,$oids);
        dd($ids);
//        set_time_limit(0);
//        if($request->isAjax()){
//            for($i=0;$i<500;$i++){
//                Db::table("user")->insert(["name"=>$request->name]);
//            }
//            echo "success";die;
//        }
//        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
        return view("index");
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
        return 'hello,' . $name;
    }

    public function redis(){
        $redis = new \Redis();
        $redis->connect("127.0.0.1",6379);
        $redis->set('test',"test word");
        echo $redis->get('test');
    }

    public function test(){}
}
