<?php

$prifix = 'apis';
Route::rule($prifix.'/login', 'api/login/login');
Route::group($prifix,function (){
    Route::rule('index/index', 'index/index');
    Route::rule('index/index1', 'index1/index1');
})->prefix('api/')->middleware(['check_token']);