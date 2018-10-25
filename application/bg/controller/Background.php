<?php
namespace app\bg\controller;

use think\App;
use think\Controller;

class Background extends Controller
{
    protected $userinfo;
    public function __construct()
    {
        if(empty(session('user.info'))){
            return redirect(url('bs/login/index'));
        }
    }
}