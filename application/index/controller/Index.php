<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return 123;
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
}
