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
    protected $loginUserinfo;

    public function __construct(Request $request)
    {
        parent::__construct();
        cookie("ms_currentUrl",$request->url());
        $this->module = strtolower($request->module());
        $this->contrller = strtolower($request->controller());
        $this->method = strtolower($request->action());
        $this->useModel = ucfirst($this->contrller);
        $this->checkLogin();
        $this->checkAuth();
        $this->assign("logoLink",Menu::getLogoLink());
    }

    /*
     * 不存在的路由的处理
     * */
    public function _empty(){
        cookie("ms_currentUrl",null);
        return view("error/404");
    }

    /*
     * 验证登录
     * */
    protected function checkLogin(){
        $model = new User();
        if(!$model->checkLogin()) $this->redirect('bs/index/index');
    }

    /*
     * 验证权限
     * */
    public function checkAuth(){
        $model = new User();
        $url = $this->module."/".$this->contrller."/".$this->method;
        if(!$model->checkAuth($url)) $this->redirect('bs/index/abort');
        $userinfo = $model->getLoginInfo();
        $this->loginUserinfo = $userinfo;
        $this->assign("loginInfo",$userinfo);//登录用户信息
        $menuModel = new Menu();
        $menuid = $menuModel->where("route","like",$url.'%')->value('id');
        $pids = $menuModel->getPids($menuid);
        $this->assign("nav",$menuModel->getNav($this->loginUserinfo->role->access,$pids,$url));//左侧菜单
        $menus = $this->getOptionMenu();
        $this->assign('menus',$menus);
        $this->assign('tab',$menuModel->getTab($pids,$menuid));//内容导航栏
    }

    /*
     * 内容页配置
     * */
    public function getOptionMenu($usemodel='',$choose=''){
        $usemodel = !empty($usemodel) ? $usemodel : $this->useModel;
        $choose = !empty($choose) ? $choose : $this->choose;
        $menu = $this->getOption($usemodel,$choose);
        return $menu->{$choose};
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
     * 页面模板
     * */
    public function show($action='',$data=[]){
        $action = !empty($action) ? $action : $this->method;
        $executetime = microtime(true) - EXECUTE_TIME;
        $executetime = round($executetime,3);
        $data['execute_time'] = $executetime;
        return view(strtolower($action),$data);
    }

    /*
     * 列表页
     * */
    public function index(Request $request){
        return $this->show(strtolower($request->action()));
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
            $role = Role::get(1);
            if(!empty($role)){
                $role->access = $role->access.",".$model->id;
                $role->save();
            }else{
                Role::create(['title'=>'超级管理员',"access"=>"1,2,3,4"]);
            }
            $this->success('新加成功',null,'',1);
        }
        return $this->show(strtolower($request->action()));
    }
}