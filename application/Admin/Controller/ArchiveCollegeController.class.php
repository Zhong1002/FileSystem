<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ArchiveCollegeController extends AdminbaseController{

	function index(){
		if(get_current_admin_id()==1){
			$role_array=M('Role')->getField('id',true);
		}
		else{
			$role_array=M('RoleUser')->where('user_id='.get_current_admin_id())->getField('role_id',true);
		}
		
		if(!$role_array){
			$this->error("你没有权限审核档案,请联系管理员");
		}
	    import("Tree");
	    $tree = new \Tree();
	    $parentid = intval(I("type_id"));
	    $key=I("keyword");
	    $type_map['role_id']=array('in',$role_array);
	    $result = D('ArchiveTypeView')->where($type_map)->select();
	    $type_array=array();
	    foreach ($result as $r) {
	        $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
	        $array[] = $r;
	        $type_array[]=$r['id'];
	    }
	    $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
	    $tree->init($array);
	    $select_categorys = $tree->get_tree(0, $str);
	    $this->assign("select_categorys", $select_categorys);
	    
	   
	    $archive_map['status']=3;
	    $archive_map['share_type']=array('in','2,3');
	    
	    if($parentid&&in_array($parentid, $type_array)){
	        $archive_map['type_id']=$parentid;
	    }
	    else{
	    	$archive_map['type_id']=array('in',$type_array);
	    }
	    
	    if($key){
	        $archive_map['name']=array('like','%'.$key.'%');
	    }
	    
	    $sn=I("sn");
	    if($sn){
	    	$archive_map['archive_sn']=array('like','%'.$sn.'%');
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
		$archive=D('ArchiveView')->where('archive_id='.$id)->find();
		if(!$archive){
			$this->error("档案不存在!");
		}
		if($archive['status']!=2){
			$this->error("档案状态不对");
		}
		
		if(get_current_admin_id()==1){
			$role_array=M('Role')->getField('id',true);
		}
		else{
			$role_array=M('RoleUser')->where('user_id='.get_current_admin_id())->getField('role_id',true);
		}
		
	    import("Tree");
	    $tree = new \Tree();
	    $type_map['role_id']=array('in',$role_array);
	    $result = D('ArchiveTypeView')->where($type_map)->select();
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
			//var_dump(I('param.'));
			$archive=I('post.archive');
			$archive['date_time']=str_replace('-', '', $archive['date_time']);
			$archive['archive_sn']=$this->get_sn($archive['type_id'], $archive['date_time']);
			$archive['status']=3;
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
			
			$this->success("修改成功！",U('ArchiveCheck/index'));
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
		$this->display(':archive_detail');
	}

	
	private function get_sn($type_id,$date_time){
		$map['type_id']=$type_id;
		$map['date_time']=$date_time;
		$map['status']=3;
		$count=M('Archive')->where($map)->count();
		
		$type=M('ArchiveType')->where('id='.$type_id)->find();
		
		if($count){
			return $type['type_sn'].$date_time.sprintf('%03d',($count+1));
		}
		else{
			return $type['type_sn'].$date_time.'001';
		}
		
	}
	
	
}