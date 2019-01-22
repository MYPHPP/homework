<?php
namespace app\bs\controller;

use app\bs\Base;
use app\common\model\Menu as Menus;
use app\common\model\Role;

class Menu extends Base {
    public function index()
    {
        $model = new Menus();
        $list = $model->getList();
//        $data = $model->getShowList();
//        $total = count($data);
//        $list = array_slice($data,0,3);
        $this->assign([
            'lists'     => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }

    public function add(){
        $model = new Menus();
        if($this->request->isPost()){
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

    public function edit($id){
        $model = new Menus();
        $data = $model->find($id);
        if(!empty($data)){
            if($this->request->isPost()){
                $data = $this->request->post();
                $data = array_filter($data);
                $validate = $model->validateData($data);
                if(!$validate['status']) return $this->error($validate['msg']);
                if($model->save($data,['id'=>$id])){
                    $this->success('操作成功！');
                }else{
                    $this->error('操作失败,请检查后重新提交！');
                }
            }
            $this->assign('data',$data);
            $this->assign('options',config('setting.menu.position'));
            $this->assign('menus',$model->getMenu());
            return $this->fetch();
        }else{
            $this->error('数据非法');
        }

    }

    public function menuPage($limit,$total){
        $html = '';
        $page = ceil($total/$limit);
        if($page > 1){
            $html .= '';
        }
        for($i=1;$i<=$page;$i++){

        }
    }
}