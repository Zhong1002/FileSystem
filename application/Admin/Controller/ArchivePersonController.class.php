<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ArchivePersonController extends AdminbaseController{

	function index(){
	    import("Tree");
	    $tree = new \Tree();
	    $parentid = intval(I("type_id"));
	    $key=I("keyword");
	    $result = D('ArchiveTypeView')->select();
	    foreach ($result as $r) {
	        $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
	        $array[] = $r;
	    }
	    $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
	    $tree->init($array);
	    $select_categorys = $tree->get_tree(0, $str);
	    $this->assign("select_categorys", $select_categorys);
	    
	    $archive_map['user_id']=get_current_admin_id();
	    if($parentid){
	        $archive_map['type_id']=$parentid;
	    }
	    
	    if($key){
	        $archive_map['name']=array('like','%'.$key.'%');
	    }
	    
		$count=D('Archive')->where($archive_map)->count();
		$page = $this->page($count, 20);
		$archive_list = D('ArchiveView')
		->where($archive_map)
		->limit($page->firstRow . ',' . $page->listRows)
		->select();

		$this->assign("page", $page->show('Admin'));
		$this->assign("archive_list",$archive_list);
		$this->assign("params",I("param."));
		$this->display();
	}
	
	
	/**
	 *  添加
	 */
	public function add() {
	    import("Tree");
	    $tree = new \Tree();
	    $result = D('ArchiveTypeView')->select();
	    foreach ($result as $r) {
	        $array[] = $r;
	    }
	    $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
	    $tree->init($array);
	    $select_categorys = $tree->get_tree(0, $str);
	    $this->assign("select_categorys", $select_categorys);
	    $this->display();
	}
	

	
}