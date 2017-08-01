<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ArchiveAllController extends AdminbaseController{

	function index(){
	    import("Tree");
	    $tree = new \Tree();
	    $parentid = intval(I("type_id"));
	    $key=I("keyword");
	    $sn=I("sn");
	    $user_id=I("user_id");
	    $result = D('ArchiveTypeView')->select();
	    $type_array=array();
	    foreach ($result as $r) {
	        $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
	        $array[] = $r;
	    }
	    $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
	    $tree->init($array);
	    $select_categorys = $tree->get_tree(0, $str);
	    $this->assign("select_categorys", $select_categorys);
	    
	    if($parentid&&in_array($parentid, $type_array)){
	        $archive_map['type_id']=$parentid;
	    }
	    if($key){
	        $archive_map['name']=array('like','%'.$key.'%');
	    }
	    
	    if($sn){
	    	$archive_map['archive_sn']=array('like','%'.$sn.'%');
	    }
	    
	    if($user_id){
	    	$archive_map['user_id']=$user_id;
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
		
		$user_list=M('Users')->field('id,user_nicename')->where('id>1')->select();
		$this->assign("user_list",$user_list);
		$this->display();
	}
	

	
	public function detail(){
		$id = intval(I("get.id"));
		$archive=D('ArchiveView')->where('archive_id='.$id)->find();
		if(!$archive){
			$this->error("档案不存在!");
		}
		$file_list=M('ArchiveFile')->where('archive_id='.$id)->select();
		$this->assign("file_list", $file_list);
		$this->assign("data", $archive);
		$this->display(':archive_detail');
	}

	
	
	
}