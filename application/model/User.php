<?php
namespace app\model;

use think\Model;

class User extends Model
{
    const VERIFYCODE = false;
    protected $pk = 'id';

    /**
     * Description 验证登录信息
     * @CreateTime 2018/10/26 16:29:05
     * @param $username
     * @param $password
     * @return array|bool|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkLogin($username ,$password){
        $username = trim($username);
        $password = trim($password);
        $user = $this->where('username','=',$username)->find();
        if(!empty($user) && aesdecrypt($user->password) == $password){
            return $user;
        }else{
            return false;
        }
    }
}