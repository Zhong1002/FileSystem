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
	
	/**
	 *  添加
	 */
	public function add_post() {
		//var_dump(I('param.'));
		$archive=I('post.archive');
		$archive['user_id']=get_current_admin_id();
		$archive['date_time']=str_replace('-', '', $archive['date_time']);
		$archive_id=M('Archive')->add($archive);
		if(!$archive_id){
			$this->error("添加失败！");
		}
		$archive['archive_id']=$archive_id;
		$files_url=I('post.files_url');
		$files_name=I('post.files_name');
		$files_size=I('post.files_size');
		if($files_url){
			$fileManager=new \Util\Controller\FileManager(get_current_admin_id());
			$file_array=array();
			foreach ($files_url as $key=>$url){
				$file=array();
				$file['archive_id']=$archive_id;
				$file['name']=$files_name[$key];
				$file['size']=$files_size[$key];
				$file['address']=$fileManager->person_save($archive, $url);
				$file['create_time']=time();
				$file['update_time']=time();
				$file_array[]=$file;
			}
			
			M('ArchiveFile')->addAll($file_array);
		}
		
		$this->success("添加成功！");
	}
	
	
	/**
	 *  删除
	 */
	public function delete() {
		$id = intval(I("get.id"));
		$archive=D('Archive')->where('archive_id='.$id)->find();
		if(!$archive){
			$this->error("档案不存在!");
		}
		if($archive['status']!=1){
			$this->error("上传后的档案不能再修改");
		}
		if (M('Archive')->delete($id)!==false) {
			$filemanager=new \Util\Controller\FileManager(get_current_admin_id());
			$filemanager->delete($archive);
			$this->success("删除档案成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	/**
	 *  删除
	 */
	public function delete_file() {
		$id = intval(I("get.id"));
		$archive_file=D('ArchiveFile')->where('file_id='.$id)->find();
		if(!$archive_file){
			$this->error("文件不存在!");
		}
		if (M('ArchiveFile')->delete($id)!==false) {
			$filemanager=new \Util\Controller\FileManager(get_current_admin_id());
			$filemanager->delete_file($archive_file);
			$this->success("删除档案成功！");
		} else {
			$this->error("删除失败！");
			
		}
	}
	/**
	 *  编辑
	 */
	public function edit() {
		$id = intval(I("get.id"));
		$archive=D('Archive')->where('archive_id='.$id)->find();
		if(!$archive){
			$this->error("档案不存在!");
		}
		if($archive['status']!=1){
			$this->error("上传后的档案不能再修改");
		}
		
		import("Tree");
		$tree = new \Tree();
		$result = D('ArchiveTypeView')->select();
		foreach ($result as $r) {
			$r['selected'] = $r['id'] == $archive['type_id'] ? 'selected' : '';
			$array[] = $r;
		}
		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
		$tree->init($array);
		$select_categorys = $tree->get_tree(0, $str);
		$this->assign("select_categorys", $select_categorys);
		
		
		$file_list=M('ArchiveFile')->where('archive_id='.$id)->select();
		$this->assign("file_list", $file_list);
		$this->assign("data", $archive);
		$this->display();
	}
	
	/**
	 *  编辑
	 */
	public function edit_post() {
		if (IS_POST) {
			var_dump(I('param.'));
			$archive=I('post.archive');
			$archive['date_time']=str_replace('-', '', $archive['date_time']);
			M('Archive')->save($archive);
			
			$files_url=I('post.files_url');
			$files_name=I('post.files_name');
			$files_size=I('post.files_size');
			if($files_url){
				$fileManager=new \Util\Controller\FileManager(get_current_admin_id());
				$file_array=array();
				foreach ($files_url as $key=>$url){
					$file=array();
					$file['archive_id']=$archive['archive_id'];
					$file['name']=$files_name[$key];
					$file['size']=$files_size[$key];
					$file['address']=$fileManager->person_save($archive, $url);
					$file['create_time']=time();
					$file['update_time']=time();
					$file_array[]=$file;
				}
				
				M('ArchiveFile')->addAll($file_array);
			}
			
			$this->success("修改成功！");
		}
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
		$this->display();
	}
	
	
	public function up(){
		$id = intval(I("get.id"));
		$archive=D('ArchiveView')->where('archive_id='.$id)->find();
		if(!$archive){
			$this->error("档案不存在!");
		}
		if($archive['status']!=1){
			$this->error("上传后的档案不能再修改");
		}
		
		$archive['status']=2;
		M('Archive')->save($archive);
		$this->success("上传成功！");
	}
	
	
}