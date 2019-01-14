<?php
namespace app\bs\controller;

use app\bs\Base;
use think\Request;

class Dashboard extends Base{
    public function index(Request $request)
    {
        return $this->fetch();
    }

    public function getOptionMenu($usemodel='',$choose='')
    {
        return '';
    }
}