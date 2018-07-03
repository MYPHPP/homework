<?php
namespace app\bs\controller;

use think\Controller;
use think\Request;
use think\Validate;

class Index extends Controller{
    public function index(Request $request){
        $useModel = "User";
        $choose = "default";
        echo 123;die;
        if($request->isPost()){
            $result = $this->checkData($request,$useModel,$choose);
            if($result['status'] != 200){
                $msg = implode(',',$result['data']);
                $this->error($msg);
            }
            $checkResult = $this->checkUnique($useModel,$result['data'],$choose);
            if($checkResult){
                $modelname = "\\app\\common\\model\\".$useModel;
                $model = new $modelname;
                $model->save($result['data']);
                $this->success('新加成功');
            }else{
                $this->error("账号已存在");
            }
        }
        $menu = $this->getMenu($useModel,$choose);
        $this->assign('menus',$menu->{$choose});
        return $this->fetch('login');
    }

    public function getMenu($modelname,$choose="default"){
        $useModel = "\\app\\common\\model\\".$modelname;
        $model = new $useModel;
        $menus = $model->getCF($choose);
        foreach($menus[$choose] as $key=>$menu){
            if(empty($menu['name'])){
                $menus[$choose][$key]['name'] = $key;
            }
        }
        $result = array2obj($menus);
        return $result;
    }

    public function checkData(Request $request,$useModel,$choose="default"){
        $rule = [];
        $tipMsg = [];
        $data = [];
        $menus = $this->getMenu($useModel,$choose);
        foreach ($menus->{$choose} as $menu){
            if(!empty($menu->validate)){
                $rule[$menu->name] = $menu->validate->rule;
                $tips = explode('|',$menu->validate->rule);
                foreach ($tips as $tip){
                    if(strrpos($tip,':')){
                        $tip = substr($tip,0,strrpos($tip,':'));
                    }
                    $tipMsg[$menu->name.".".$tip] = $menu->validate->{$menu->name.".".$tip};
                }
            }
            $data[$menu->name] = filterChar($request->{$menu->name});
        }
        $result = ['status'=>200,'data'=>$data];
        if(!empty($rule)){
            $validate = Validate::make($rule,$tipMsg)->batch();
            if(!$validate->check($data)){
                $result = ['status' => 301,'data'=>$validate->getError()];
            }
        }
        return $result;
    }

    public function checkUnique($modelname,$data,$choose="default"){
        $check = [];
        $useModel = "\\app\\common\\model\\".$modelname;
        $model = new $useModel;
        $menus = $this->getMenu($modelname,$choose);
        foreach($menus->{$choose} as $menu){
            if(isset($menu->unique) && $menu->unique == true){
                $check[] = [$menu->name=>$data[$menu->name]];
            }
        }
        $res = true;
        if(!empty($check)){
            if(count($check) == 1){
                $query = $model->where($check);
            }else{
                $query = $model->where($check[0]);
                unset($check[0]);
                foreach($check as $val){
                    $query = $query->whereOr($val);
                }
            }
            $result = $query->find();
            if(!empty($result)){
                $res = false;
            }
        }
        return $res;
    }
}