<?php
namespace app\bs\controller;

use app\bs\model\Role;

class Auth extends Base{
    public function index()
    {
        $model = new Role();
        $where = [];
        $order = '';
        $pageParam = [];
        $lists = $model->getList($where ,$order ,$this->pageRowNum ,$pageParam);
        $this->assign([
            'lists' => $lists,
            'total' => $lists->total(),
            'page' => $lists->render(),
        ]);
        return $this->fetch();
    }

    public function edit(){
        return $this->fetch();
    }
}