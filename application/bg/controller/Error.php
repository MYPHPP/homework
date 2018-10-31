<?php
namespace app\bg\controller;

use think\Controller;

class Error extends Controller
{
    public function index(){
        return view('error/500');
    }
}