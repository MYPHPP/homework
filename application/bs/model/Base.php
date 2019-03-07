<?php
namespace app\bs\model;

use think\Model;
use think\model\concern\SoftDelete;

class Base extends Model{
    use SoftDelete;
    protected $pk = "id";//设置主键
    protected $deleteTime = "delete_time";//设置软删除字段

    public function getList($where=[] ,$order='' ,$pagenum=10 ,$pageParam){
        $model = $this;
        if(!empty($where)){
            $model = $model->where($where);
        }
        if(!empty($order)){
            $model = $model->order($order);
        }else{
            $model = $model->order('id desc');
        }
        return $model->paginate($pagenum,false ,$pageParam);
    }

    public function getList1($website=['num'=>10,'page_site'=>[]] ,$where='' ,$order=''){
        $model = $this->paginate($website['num'],false,$website['page_site']);
        if(!empty($where)){
            $model->where($where);
        }
        if(!empty($order)){
            $model->order($order);
        }
        return $model;
    }
}