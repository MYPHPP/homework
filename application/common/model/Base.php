<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Base extends Model{
    use SoftDelete;
    protected $pk = "id";//设置主键
    protected $deleteTime = "delete_time";//设置软删除字段
}