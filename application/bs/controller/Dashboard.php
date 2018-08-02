<?php
namespace app\bs\controller;

use app\bs\Base;
use think\Request;

class Dashboard extends Base{
    public function index(Request $request)
    {
        return $this->show();
    }

    public function getOptionMenu($usemodel='',$choose='')
    {
        return '';
    }
}