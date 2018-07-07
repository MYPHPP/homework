<?php
namespace app\bs\controller;

use app\bs\Base;
use app\custom\Crypto;

class Dashboard extends Base{
    public function index(){
        dd(cookie('currentUrl'));
    }

    public function test(){
        $model = new Crypto();
        $m = $model->aesencrypt("eeewww");
        $y = $model->aesdecrypt($m);
        echo $m;
        echo "<br/>";
        echo $y;
    }
}