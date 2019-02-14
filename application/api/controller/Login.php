<?php
namespace app\api\controller;

use app\api\model\User;
use think\Controller;
use think\Request;

class Login extends Controller
{
    public function login(Request $request){
        $model = new User();
        return $model->createToken(1);
    }
}