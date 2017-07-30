<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ArchiveFileController extends AdminbaseController{
	
	public function tk(){
		$data['message']="";
		$data['token']=uniqid();
		$data['success']=true;
		exit(json_encode($data));
	}
	
	public function upload(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->rootPath  = "data/upload/";
		$upload->maxSize = 10 * 1024 * 1024 ;// 设置附件上传大小
		$upload->exts = array('','jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath = ''; // 设置附件上传（子）目录
		$upload->autoSub = false; // 不创建子目录
		// 上传文件
		$info = $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$data['message']=$upload->getError();
			$data['start']=0;
			$data['success']=false;
			exit(json_encode($data));
		}
		$start=0;
		foreach ( $info as $file ) {
			$start+=$file['size'];
			$file_path=$upload->rootPath.$upload->savePath.$file ['savepath'] . $file ['savename'];
			$data['message']=$file_path;
		}
		
		$data['start']=$start;
		$data['success']=true;
		exit(json_encode($data));
	}
}