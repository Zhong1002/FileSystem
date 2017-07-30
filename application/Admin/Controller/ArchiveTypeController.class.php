<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ArchiveTypeController extends AdminbaseController{
	function index(){
        $result = D('ArchiveTypeView')->select();
        import("Tree");
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $typelist=array();
        foreach ($result as $m){
        	$typelist[$m['id']]=$m;
        	 
        }
        foreach ($result as $n=> $r) {
        	
        	$result[$n]['level'] = $this->_get_level($r['id'], $typelist);
        	$result[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';
        	
            $result[$n]['str_manage'] = '<a href="' . U("ArchiveType/add", array("parentid" => $r['id'])) . '">添加子菜单</a> | <a href="' . U("ArchiveType/edit", array("id" => $r['id'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("ArchiveType/delete", array("id" => $r['id']) ). '">删除</a> ';
            $result[$n]['status'] = $r['status'] ? "显示" : "隐藏";
            $result[$n]['role_name'] = $r['role_name'] ? $r['role_name']: "未设定";
          
        }

        $tree->init($result);
        $str = "<tr id='node-\$id' \$parentid_node>
                    <td style='padding-left:20px;'></td>
					<td>\$id</td>
					<td>\$type_sn</td>
        			<td>\$spacer\$name</td>
					<td>\$content</td>
					<td>\$role_name</td>
				    <td>\$status</td>
					<td>\$str_manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
        $this->display();
	}
	
	
	/**
	 * 获取类型深度
	 * @param $id
	 * @param $array
	 * @param $i
	 */
	protected function _get_level($id, $array = array(), $i = 0) {
	
	    if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
	        return  $i;
	    }else{
	        $i++;
	        return $this->_get_level($array[$id]['parentid'],$array,$i);
	    }
	
	}
	
	
	/**
	 *  添加
	 */
	public function add() {
	    import("Tree");
	    $tree = new \Tree();
	    $parentid = intval(I("get.parentid"));
	    $result = D('ArchiveTypeView')->select();
	    foreach ($result as $r) {
	        $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
	        $array[] = $r;
	    }
	    $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
	    $tree->init($array);
	    $select_categorys = $tree->get_tree(0, $str);
	    $this->assign("select_categorys", $select_categorys);
	    
	    $role_list=M('Role')->where('id>1')->select();
	    $this->assign("role_list", $role_list);
	    $this->display();
	}
	
	/**
	 *  添加
	 */
	public function add_post() {
	    if (IS_POST) {
	        $type_model=M('ArchiveType');
	        if ($type_model->create()) {
	            $type_model->create_time=time();
	            if ($type_model->add()!==false) {
	               
	                $this->success("添加成功！", U('ArchiveType/index'));
	            } else {
	                $this->error("添加失败！");
	            }
	        } else {
	            $this->error($type_model->getError());
	        }
	    }
	}
	
	/**
	 *  删除
	 */
	public function delete() {
	    $id = intval(I("get.id"));
	    $count = M('ArchiveType')->where(array("parentid" => $id))->count();
	    if ($count > 0) {
	        $this->error("该菜单下还有子菜单，无法删除！");
	    }
	    if (M('ArchiveType')->delete($id)!==false) {
	        $this->success("删除菜单成功！");
	    } else {
	        $this->error("删除失败！");
	    }
	}
	
	/**
	 *  编辑
	 */
	public function edit() {
	    import("Tree");
	    $tree = new \Tree();
	    $id = intval(I("get.id"));
	    $rs = D('ArchiveTypeView')->where(array("id" => $id))->find();
	    $result = D('ArchiveTypeView')->select();
	    foreach ($result as $r) {
	        $r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
	        $array[] = $r;
	    }
	    $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
	    $tree->init($array);
	    $select_categorys = $tree->get_tree(0, $str);
	    $this->assign("data", $rs);
	    $this->assign("select_categorys", $select_categorys);
	    $role_list=M('Role')->where('id>1')->select();
	    $this->assign("role_list", $role_list);
	    $this->display();
	}
	
	/**
	 *  编辑
	 */
	public function edit_post() {
	    if (IS_POST) {
	        $type_model=M('ArchiveType');
	        if ($type_model->create()) {
	            if ($type_model->save() !== false) {
	               
	                $this->success("更新成功！");
	            } else {
	                $this->error("更新失败！");
	            }
	        } else {
	            $this->error($type_model->getError());
	        }
	    }
	}
	
}