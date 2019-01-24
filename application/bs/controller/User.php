<?php
namespace app\bs\controller;

use app\bs\Base;
use app\common\model\User as Users;

class User extends Base{
    public function index()
    {
        return $this->fetch();
    }
}