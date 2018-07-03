<?php
namespace app\bs;

use app\common\model\Menu;
use think\cache\driver\Redis;
use think\Controller;
use think\Request;

class Base extends Controller {
    public function __construct(Request $request)
    {
        $auth = false;
        $module = strtolower($request->module());
        $method = strtolower($request->controller());
        $action = strtolower($request->action());
        $menuid = Menu::where(['module'=>$module,'method'=>$method,'action'=>$action])->value('id');
        $redis = new Redis();
        $redis->set('ddd',4566);
    }
}