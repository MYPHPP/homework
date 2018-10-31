<?php
namespace app\bg\controller;


class News extends Background
{
    public function index(){
        return view();
    }

    public function games(){
        return view('index');
    }
}