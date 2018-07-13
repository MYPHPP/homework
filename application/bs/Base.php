<?php
namespace app\bs;

use app\common\model\Menu;
use think\cache\driver\Redis;
use think\Controller;
use think\Request;
use think\Validate;

class Base extends Controller {
    protected $module;
    protected $method;
    protected $contrller;
    protected $useModel;
    protected $choose = "default";

    public function __construct(Request $request)
    {
        cookie("currentUrl",$request->url());
        if(empty(session('login_id'))){
            $this->redirect('bs/index/index');
        }
        $this->module = strtolower($request->module());
        $this->contrller = strtolower($request->controller());
        $this->method = strtolower($request->action());
        $this->useModel = ucfirst($this->contrller);
    }

    public function _empty(){
        cookie("currentUrl",null);
        echo 456;
    }

    protected function checkAuth(Request $request){
        $auth = false;
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
            if(isset($request->{$menu->name})){
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
        }
        $result = ['status'=>200,'data'=>$data];
        if(!empty($rule)){
            //$validate = Validate::make($rule,$tipMsg)->batch();//批量验证
            $validate = Validate::make($rule,$tipMsg);//单个验证
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
            if(isset($data[$menu->name])){
                if(isset($menu->unique) && $menu->unique == true){
                    $check[] = [$menu->name=>$data[$menu->name]];
                }
            }
        }
        $res = true;
        if(!empty($check)){
            $query = $model->where($check[0]);
            if(count($check) > 1){
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

    public function index(Request $request){
        $menu = $this->getMenu($this->useModel,$this->choose);
        return view(strtolower($request->action()),['menus'=>$menu->{$this->choose}]);
    }
}