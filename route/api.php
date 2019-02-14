<?php

$prifix = 'apis';
Route::rule($prifix.'/login', 'api/login/login');
Route::group($prifix,function (){
    Route::rule('index/index', 'index/index');
})->prefix('api/')->middleware(['check_login']);