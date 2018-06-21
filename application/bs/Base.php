<?php
namespace app\bs;

use think\Controller;
use think\Request;

class Base extends Controller{
    protected $controller;
    protected $method;

    public function __construct(Request $request)
    {
        $this->controller = $request->baseUrl();
        echo $this->controller;die;
    }
}