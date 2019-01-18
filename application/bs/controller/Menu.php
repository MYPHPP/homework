<?php
namespace app\bs\controller;

use app\bs\Base;
use app\common\model\Menu as Menus;

class Menu extends Base {
    public function index()
    {
        $model = new Menus();
        $list = $model->getList();
        $this->assign([
            'lists'     => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }

    public function add(){
        return $this->fetch();
    }
}