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
    protected $request;
    protected $choose = "default";
    protected $loginUserinfo;

    public function __construct(Request $request)
    {
        parent::__construct();
        if(!strpos($request->url(),'about')){
            cookie("ms_currentUrl",$request->url());
        }
        $this->request = $request;
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
        $this->assign("nav",$menuModel->getCategory($this->loginUserinfo->role->access,$pids,$menuid));//左侧菜单
        $this->assign('tab',$menuModel->getTab($pids,$menuid));//内容导航栏
    }

    /*
     * 列表页
     * */
    public function index(Request $request){
        return $this->fetch();
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
        return $this->fetch();
    }

    public function edit(){
        return $this->fetch('add');
    }
}