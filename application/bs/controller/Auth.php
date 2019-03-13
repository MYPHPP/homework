<?php
namespace app\bs\controller;

use app\bs\model\Menu;
use app\bs\model\Role;

class Auth extends Base{
    public function index()
    {
        $model = new Role();
        $where = [];
        $order = '';
        $pageParam = [];
        $lists = $model->getList($where ,$order ,$this->pageRowNum ,$pageParam);
        $this->assign([
            'lists' => $lists,
            'total' => $lists->total(),
            'page' => $lists->render(),
        ]);
        return $this->fetch();
    }

    public function edit(){
        if($this->request->isPost()){
            if($this->request->param('type') == 'choose'){
                return $this->edit_choose();
            }
            if($this->request->param('type') == 'up'){
                return $this->edit_up();
            }
            $this->error('非法提交');
        }
        $model = new Role();
        $id = $this->request->param('id');
        $role = $model->find($id);
        if(!$role) $this->error('没有该角色');
        $access = [];
        if(!empty($role->access)){
            $access = explode(',',$role->access);
        }
        $this->assign('role_access',$this->getRoleList($access));
        return $this->fetch();
    }

    /**
     * 获取权限列表和已有的权限选中
     * @param $access
     * @param int $pid
     * @param string $html
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRoleList($access,$pid=0,$html=''){
        $menu = Menu::where('pid',$pid)->order('sort desc')->field('id,title')->select();
        if(!empty($menu)){
            if($pid == 0){
                $html .= '<ul style="list-style-type:none">';
            }else{
                $html .= '<li><ul style="list-style-type:none">';
            }
            foreach($menu as $m){
                $check = '';
                if(in_array($m->id,$access)){
                    $check = 'checked';
                }
                $html .= '<li><input data-id="'.$m->id.'" type="checkbox" name="aids[]" value="'.$m->id.'" class="access-input access-input-'.$m->id.'" '.$check.'>'.$m->title.'</li>';
                $html = $this->getRoleList($access,$m->id,$html);
            }
            if($pid == 0){
                $html .= '</ul>';
            }else{
                $html .= '</ul></li>';
            }
        }
        return $html;
    }

    public function edit_choose(){
        $id = $this->request->param('mid');
        $model = new Menu();
        $menu = $model->find($id);
        if(!$menu) $this->error('不存在该菜单');
        $child = $model->getChildMenuids($menu->id);
        $parentid = [];
        if($menu->pid != 0){
            $parentid = $model->getPids($menu->id);
        }
        $arr['cids'] = $child;
        $arr['pids'] = $parentid;
        $this->success(json_encode($arr));
    }

    public function edit_up(){
        $role = Role::find($this->request->param('id'));
        if(!$role) $this->error('角色不存在');
        $access = $this->request->param('aids');
        sort($access);
        $role->access = implode(',',$access);
        $role->save();
        $this->success('修改成功');
    }
}