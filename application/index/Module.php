<?php
namespace app\index;

use think\Controller;

class Module extends Controller{
    protected $module;
    protected $controller;
    protected $method;
    public function __construct()
    {

    }

    public function checkRole(){

    }

    public function test(){
        echo "hello word!!!";
    }
}