<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Base extends Model{
    use SoftDelete;
    protected $pk = "id";//设置主键
    protected $deleteTime = "delete_time";//设置软删除字段

    /**
     * 获取列表数据
     * @param $website array 分页相关参数
     * @param $where array 查询条件
     * @param $order string 排序条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($website=['num'=>10,'page_site'=>[]] ,$where='' ,$order=''){
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