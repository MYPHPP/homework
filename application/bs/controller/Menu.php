<?php
namespace app\bs\controller;

use app\bs\Base;
use app\common\model\Menu as Menus;
use app\common\model\Role;
use app\common\validate\Menu as ValidateMenu;

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
        $model = new Menus();
        if($this->request->isAjax()){
            $data = $this->request->param();
            $data = array_filter($data);
            $validate = $model->validateData($data);
            if(!$validate['status']) return $this->error($validate['msg']);
            if($model->save($data)){
                $role = Role::get(1);
                $role->access = $role->access.','.$model->id;
                $role->save();
                $this->success('操作成功！');
            }else{
                $this->error('操作失败,请检查后重新提交！');
            }
        }
        $this->assign('options',config('setting.menu.position'));
        $this->assign('menus',$model->getMenu());
        return $this->fetch();
    }
}