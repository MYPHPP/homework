<?php
namespace app\bs\controller;

use app\common\model\User;
use think\Controller;
use think\Request;
use think\Validate;

class Index extends Controller{
    protected $useModel = "User";
    protected $choose = "default";
    /*
     * 登录
     * */
    public function index(Request $request){
        if(!empty(cookie('login_id'))){
            session('login_id',cookie('login_id'));
            if(!empty(cookie("currentUrl"))){
                return redirect(cookie("currentUrl"));
            }else{
                return redirect('bs/dashboard/index');
            }
        }
        if($request->isPost()){
            $result = $this->checkData($request,$this->useModel,$this->choose);
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
        $menu = $this->getMenu($this->useModel,$this->choose);
        $this->assign('menus',$menu->{$this->choose});
        return $this->fetch('login');
    }

    /*
     * 注册
     * */
    public function register(Request $request){
        if($request->isPost()){
            $result = $this->checkData($request,$this->useModel,$this->choose);
            if($result['status'] != 200){
                if(is_array($result['data'])){
                    $msg = implode(',',$result['data']);
                }else{
                    $msg = $result['data'];
                }
                $this->error($msg);
            }
            $checkResult = $this->checkUnique($this->useModel,$result['data'],$this->choose);
            if($checkResult){
                $modelname = "\\app\\common\\model\\".$this->useModel;
                $model = new $modelname;
                $model->save($result['data']);
                $this->success('新加成功','bs/index/index');
            }else{
                $this->error("账号已存在");
            }
        }
        $menu = $this->getMenu($this->useModel,$this->choose);
        $this->assign('menus',$menu->{$this->choose});
        return $this->fetch('register');
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
}
