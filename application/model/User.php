<?php
namespace app\model;

use think\Model;

class User extends Model{
    protected $pk = 'id';
    protected $table = 'dev_user';
}