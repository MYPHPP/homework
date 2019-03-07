<?php
namespace app\bs\controller;

use app\bs\model\Menu;
use app\bs\model\User;
use think\Controller;
use think\facade\Env;
use think\Request;

class Base extends Controller {
    protected $module;
    protected $method;
    protected $contrller;
    protected $useModel;
    protected $request;
    protected $loginUserinfo;
    protected $pageRowNum;

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
        $auth = $this->checkAuth();
        if(false === $auth){
            $html = file_get_contents(Env::get('app_path').'bs/view/404.html');
            echo $html;die;
        }
        $pagenum = cookie('pr_pagerow') ? cookie('pr_pagerow') : 10;
        $this->pageRowNum = $pagenum;
        $this->assign("logoLink",Menu::getLogoLink());
        $this->assign('pagerow', $pagenum);

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
        if(!$model->checkAuth($url)){
            if($this->request->isAjax()){
                $this->error('没有改操作的权限');
            }else{
                return false;
            }
        }
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
     * 批量删除所选数据
     * */
    public function delAll(){
        if($this->request->isAjax()){
            try{
                $file = Env::get('APP_PATH').'common/model/'.$this->useModel.'.php';
                if(is_file($file)){
                    $modelname = "\\app\\common\\model\\".$this->useModel;
                }else{
                    throw new \Exception('该模块模型不存在，请确认后重新提交');
                }
            }catch (\Exception $e){
                return $this->error($e->getMessage());
            }
            $model = new $modelname;
            $ids = $this->request->param('ids');
            if(!empty($ids)){
                if($model->whereIn('id',$ids)->update(['delete_time' => time()])){
                    $this->success('处理成功');
                }else{
                    $this->error('处理失败');
                }
            }else{
                $this->error('请选择数据');
            }
        }
    }

    /**
     * 删除选中的单条数据
     */
    public function delete(){
        if($this->request->isAjax()){
            try{
                $file = Env::get('APP_PATH').'common/model/'.$this->useModel.'.php';
                if(is_file($file)){
                    $modelname = "\\app\\common\\model\\".$this->useModel;
                }else{
                    throw new \Exception('该模块模型不存在，请确认后重新提交');
                }
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $id = $this->request->param('id');
            $id = intval($id);
            if($id > 0){
                $model = new $modelname;
                $data = $model->find($id);
                if(!empty($data)){
                    if($data->delete()){
                        $this->success('操作成功');
                    }else{
                        $this->error('操作失败，请重试');
                    }
                }else{
                    $this->error('请正确选择要处理的数据');
                }
            }else{
                $this->error('非法数据');
            }
        }
    }
}