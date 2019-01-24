<?php
namespace app\bs\controller;

use app\bs\Base;
use app\common\model\User as Users;

class User extends Base{
    public function index()
    {
        if($this->request->isPost()){
            $data = $this->request->param();
            if(Users::update($data,['id'=>$this->loginUserinfo->id])){
                $this->success('修改成功！');
            }else{
                $this->error('修改失败，请检查数据！');
            }
        }
        return $this->fetch();
    }
}