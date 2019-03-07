<?php
namespace app\bs\controller;

class Dashboard extends Base{
    public function index()
    {
        return $this->fetch();
    }

    public function getOptionMenu($usemodel='',$choose='')
    {
        return '';
    }
}