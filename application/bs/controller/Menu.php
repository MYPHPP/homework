<?php
namespace app\bs\controller;

use app\bs\Base;
use app\bs\model\Menu as Menus;
use app\bs\model\Role;

class Menu extends Base {
    public function index()
    {
        $model = new Menus();
//        $param['query']['position'] = 1;
//        $website=['num'=>1,'page_site'=>$param];
//        $list = $model->getList($website)->each(function ($item) use ($model){
//            $parentTitle = $model->find($item['pid']);
//            if(!empty($parentTitle)){
//                $item['parentTitle'] = $parentTitle->title;
//            }else{
//                $item['parentTitle'] = '一级菜单';
//            }
//        });
//        $this->assign([
//            'lists'     => $list,
//            'total'     => $list->total(),
//            'page'      => $list->render()
//        ]);
        $length = 10;
        $offet = $this->request->param('page','1');
        $offet = intval($offet);
        if($offet < 1){
            $offet = 1;
        }
        $offet = ($offet-1)*$length;
        $data = $model->getShowList();
        $total = count($data);
        $list = array_slice($data,$offet,$length);
        $this->assign([
            'lists'     => $list,
            'total'     => $total,
            'page'     => $this->menuPage($length,$total)
        ]);
        return $this->fetch();
    }

    public function add(){
        $model = new Menus();
        if($this->request->isPost()){
            $data = $this->request->param();
            $flterVal = function ($arr){
                $needdata = [];
                if(!empty($arr)){
                    foreach($arr as $k=>$v){
                        if($v != ''){
                            $needdata[$k] = $v;
                        }
                    }
                }
                return $needdata;
            };
            $data = $flterVal($data);
            $validate = $model->validateData($data);
            if(!$validate['status']) return $this->error($validate['msg']);
            if($model->save($data)){
                $role = Role::get(1);
                $role->access = $role->access.','.$model->id;
                $role->save();
                $this->success('操作成功！');
            }else{
                $this->error('操作失败,请检查后重新提交！');
            }
        }
        $this->assign('options',config('setting.menu.position'));
        $this->assign('menus',$model->getMenu());
        return $this->fetch();
    }

    public function edit($id){
        $model = new Menus();
        $data = $model->find($id);
        if(!empty($data)){
            if($this->request->isPost()){
                $data = $this->request->post();
                $flterVal = function ($arr){
                    $needdata = [];
                    if(!empty($arr)){
                        foreach($arr as $k=>$v){
                            if($v != ''){
                                $needdata[$k] = $v;
                            }
                        }
                    }
                    return $needdata;
                };
                $data = $flterVal($data);
                $validate = $model->validateData($data);
                if(!$validate['status']) return $this->error($validate['msg']);
                if($model->save($data,['id'=>$id])){
                    $this->success('操作成功！');
                }else{
                    $this->error('操作失败,请检查后重新提交！');
                }
            }
            $this->assign('data',$data);
            $this->assign('options',config('setting.menu.position'));
            $this->assign('menus',$model->getMenu());
            return $this->fetch();
        }else{
            $this->error('数据非法');
        }

    }

    public function menuPage($limit,$total){
        $gets = $this->request->get();
        $url = $this->request->baseUrl();
        $param= '';
        $page = 1;
        if(isset($gets['page'])){
            $page = intval($gets['page']) > 0 ? intval($gets['page']) : 1;
            unset($gets['page']);
        }
        if(!empty($gets)){
            $param .= '?'.http_build_query($gets,null,'&');
            $param .= '&';
        }

        $num = 5;
        $space = ceil($num/2);
        $totalPage = ceil($total/$limit);
        $first = '';
        $prev = '';
        $next = '';
        $end = '';
        $link = '';
        if($totalPage > 1){
            if(!empty($param)){
                $param = $url.$param;
            }else{
                $param = $url.'?';
            }
            if($page>1){
                $prevpage = $page-1;
                $prev = '<li><a href="' . htmlentities($param). 'page=' . $prevpage . '"><sapn>上一页</sapn></a></li>';
            }
            if($page < $totalPage){
                $nextpage = $page+1;
                $next = '<li><a href="' . htmlentities($param). 'page='  . $nextpage . '"><sapn>下一页</sapn></a></li>';
            }
            if($totalPage > $num){
                if($page > $space){
                    $first = '<li><a href="' . htmlentities($param). 'page='  . '1"><sapn>首页</sapn></a></li>';
                }
                if($page <= $totalPage-$space){
                    $end = '<li><a href="' . htmlentities($param). 'page='  . $totalPage . '"><sapn>尾页</sapn></a></li>';
                }
            }
            if($totalPage < $num){
                $link = $this->getLinkPage($page,1,$totalPage,$param);
            }elseif($page < $space){
                $link = $this->getLinkPage($page,1,$num,$param);
            }elseif($page > $totalPage-$space){
                $link = $this->getLinkPage($page,$totalPage-$num+1,$totalPage,$param);
            }else{
                $link = $this->getLinkPage($page,$page - $space +1,$page + $space -1,$param);
            }
        }
        return sprintf('<ul class="pagination pagination-sm no-margin pull-right">%s %s %s %s %s</ul>', $first, $prev, $link, $next, $end);
    }

    public function getLinkPage($page,$start ,$end, $url){
        $html = '';
        for($i=$start ;$i<=$end ;$i++){
            $active = '';
            if($page == $i){
                $active = 'active';
            }
            $html .= '<li class="'.$active.'"><a href="' . htmlentities($url) . 'page=' .$i. '"><sapn>' . $i . '</sapn></a></li>';
        }
        return $html;
    }

    public function delAll(){
        $ids = $this->request->param('ids');
        if(!empty($ids)){
            $pids = Menus::whereIn('id',$ids)->where('pid',0)->column('id');
            if(!empty($pids)){
                foreach($pids as $pid){
                    $dids = $this->getChildIds($pid);
                    if(!empty($dids)){
                        foreach($dids as $did){
                            Menus::destroy($did);
                        }
                    }
                }
            }
        }
        parent::delAll();
    }

    public function getChildIds($pid,$ids=[]){
        if(intval($pid) > 0){
            $childs = Menus::where('pid','=',$pid)->column('id');
            if(!empty($childs)){
                foreach($childs as $child){
                    $ids[] = $child;
                    $ids = $this->getChildIds($child,$ids);
                }
            }
        }
        return $ids;
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $id = intval($id);
        if($id > 0){
            $pid = Menus::where('id',$id)->value('pid');
            if($pid == 0){
                $dids = $this->getChildIds($id);
                if(!empty($dids)){
                    foreach($dids as $did){
                        Menus::destroy($did);
                    }
                }
            }
        }
        parent::delete();
    }
}