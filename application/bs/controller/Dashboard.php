<?php
namespace app\bs\controller;

use app\bs\Base;

class Dashboard extends Base{
    public function index(){
        dd(cookie('currentUrl'));
    }

    public function test(){

    }
}