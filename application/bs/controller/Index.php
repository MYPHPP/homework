<?php
namespace app\bs\controller;

use app\bs\Base;
use app\common\model\User;
use think\Controller;
use think\Request;


class Index extends Controller{
    protected $useModel = "User";
    protected $choose = "default";
    /*
     * 登录
     * */
    public function index(Request $request){
        $quoteModel = new Base($request);
        if(!empty(cookie('login_id'))){
            session('login_id',cookie('login_id'));
            if(!empty(cookie("currentUrl"))){
                return redirect(cookie("currentUrl"));
            }else{
                return redirect('bs/dashboard/index');
            }
        }
        if($request->isPost()){
            $result = $quoteModel->checkData($request,$this->useModel,$this->choose);
            if($result['status'] != 200){
                if(is_array($result['data'])){
                    $msg = implode(',',$result['data']);
                }else{
                    $msg = $result['data'];
                }
                $this->error($msg);
            }
            $user = User::where(['name'=>$request->name,'passwd'=>$request->passwd])->field('lid,name')->find();
            if(!empty($user)){
                session('login_id',$user['lid']);
                if(!empty($request->remember)){
                    cookie("login_id",$user['lid'],7*86400);
                }
                if(!empty(cookie("currentUrl"))){
                    return redirect(cookie("currentUrl"));
                }else{
                    return redirect('bs/dashboard/index');
                }
            }else{
                $this->error('账号或密码错误');
            }
        }
        $menu = $quoteModel->getMenu($this->useModel,$this->choose);
        $this->assign('menus',$menu->{$this->choose});
        return $this->fetch('login');
    }

    /*
     * 注册
     * */
    public function register(Request $request){
        $quoteModel = new Base($request);
        if($request->isPost()){
            $result = $quoteModel->checkData($request,$this->useModel,$this->choose);
            if($result['status'] != 200){
                if(is_array($result['data'])){
                    $msg = implode(',',$result['data']);
                }else{
                    $msg = $result['data'];
                }
                $this->error($msg);
            }
            $checkResult = $quoteModel->checkUnique($this->useModel,$result['data'],$this->choose);
            if($checkResult){
                $modelname = "\\app\\common\\model\\".$this->useModel;
                $model = new $modelname;
                $model->save($result['data']);
                $this->success('新加成功','bs/index/index');
            }else{
                $this->error("账号已存在");
            }
        }
        $menu = $quoteModel->getMenu($this->useModel,$this->choose);
        $this->assign('menus',$menu->{$this->choose});
        return $this->fetch('register');
    }
}
