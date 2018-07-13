<?php
namespace app\bs\controller;

use app\bs\Base;
use think\Request;

class Dashboard extends Base{
    public function index(Request $request){
        dd(cookie('currentUrl'));
    }

    public function test(){

    }
}