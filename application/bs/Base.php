<?php
namespace app\bs;

use app\common\model\Menu;
use think\cache\driver\Redis;
use think\Controller;
use think\Request;

class Base extends Controller {
    public function __construct(Request $request)
    {
        cookie("currentUrl",$request->url());
        if(empty(session('login_id'))){
            $this->redirect('bs/index/index');
        }
        $auth = false;
        $module = strtolower($request->module());
        $method = strtolower($request->controller());
        $action = strtolower($request->action());
        $menuid = Menu::where(['module'=>$module,'method'=>$method,'action'=>$action])->value('id');
    }
}