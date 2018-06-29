<?php
namespace app\bs\controller;

use app\common\model\User;
use think\Controller;
use think\Request;
use think\Validate;

class Index extends Controller{
    public function index(Request $request){
        $useModel = "User";
        $choose = "default";
        if($request->isPost()){
            $result = $this->checkData($request,$useModel,$choose);
            if($result['status'] != 200){
                $msg = implode(',',$result['data']);
                $this->error($msg);
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
}