<?php
namespace app\bs;

use app\Original;
use think\Request;

class Base extends Original{
    public function __construct(Request $request)
    {
        $module = strtolower($request->module());
        $controller = strtolower($request->controller());
        $method = strtolower($request->action());
    }
}