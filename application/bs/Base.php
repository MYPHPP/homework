<?php
namespace app\bs;

use app\common\model\Menu;
use app\common\model\Role;
use app\common\model\User;
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
        $this->module = strtolower($request->module());
        $this->contrller = strtolower($request->controller());
        $this->method = strtolower($request->action());
        $this->useModel = ucfirst($this->contrller);
        $this->checkLogin();
        $this->checkAuth($request);
    }

    public function _empty(){
        cookie("currentUrl",null);
        return view("error/404");
    }

    /*
     * 验证登录
     * */
    protected function checkLogin(){
        $check = false;
        if(!empty(session('login_id'))){
            $user = User::get(session('login_id'));
            if(!empty($user)){
                $check = true;
            }else{
                session('login_id',null);
                cookie("login_id",null);
            }
        }
        if(!$check) $this->redirect('bs/index/index');
    }

    /*
     * 验证权限
     * */
    public function checkAuth(Request $request){
        $auth = false;
        $menus = $this->getMenu(['route']);
        if((is_object($menus) && $menus->count() >0) || !empty($menus)){
            $url = strtolower(ltrim($request->baseUrl(),'/'));
            foreach ($menus as $menu){
                $route = explode('/',strtolower($menu->route));
                $menuRoute = current($route)."/".next($route)."/".next($route);
                if($menuRoute == $url){
                    $auth = true;
                }
            }
        }
        if(!$auth) $this->redirect('bs/index/abort');
    }

    /*
     * 菜单
     * */
    protected function getMenu($fields = []){
        $role = User::where('lid',session('login_id'))->find();
        if(!empty($role) && !empty($role->role->access)){
            $model = new Menu();
            $model = $model->whereIn("id",$role->role->access);
            if(!empty($fields)){
                $model = $model->field(explode(',',$fields));
            }
            return $model->select();
        }
        return null;
    }

    /*
     * 页面显示
     * */
    public function getOption($modelname,$choose="default"){
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

    /*
     * 验证数据
     * */
    public function checkData(Request $request,$useModel,$choose="default"){
        $rule = [];
        $tipMsg = [];
        $data = [];
        $menus = $this->getOption($useModel,$choose);
        foreach ($menus->{$choose} as $menu){
            //查找验证规则
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
                if(is_array($request->{$menu->name})){
                    $request->{$menu->name} = implode(",",$request->{$menu->name});
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

    /*
     * 列表页
     * */
    public function index(Request $request){
        echo 1;
    }

   /*
    * 添加页
    * */
    public function add(Request $request){
        if($request->isPost()){
            $result=$this->checkData($request,$this->useModel,$this->choose);
            if($result['status'] != 200){
                if(is_array($result['data'])){
                    $msg = implode('<br/>',$result['data']);
                }else{
                    $msg = $result['data'];
                }
                $this->error($msg);
            }
            $modelname = "\\app\\common\\model\\".$this->useModel;
            $model = new $modelname;
            $result['data']['create_by'] = session("login_id");
            $result['data']['update_by'] = session("login_id");
            $model->save($result['data']);
            $this->success('新加成功',null,'',1);
        }
        $menu = $this->getOption($this->useModel,$this->choose);
        return view(strtolower($request->action()),['menus'=>$menu->{$this->choose}]);
    }
}