<?php
namespace app\bg\controller;

use app\model\User;
use think\Controller;

class Background extends Controller
{
    protected $userinfo;
    public function __construct()
    {
        parent::__construct();
        if(!empty(cookie('user'))){
            session('user',cookie('user'));
        }
        if(empty(session('user'))){
            return $this->redirect(url('bg/login/index'));
        }
        $this->userinfo = User::get(session('user'));
    }
}