<?php
namespace app\index;

use think\Controller;

class Module extends Controller{
    function __construct()
    {
        parent::__construct();
    }

    public function test(){
        echo "hello word!!!";
    }
}