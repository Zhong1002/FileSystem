<?php
namespace Admin\Controller;
use Think\Controller;
class ArchiveFileController extends Controller{
	
	public function tk(){
		$data['message']="";
		$data['token']=uniqid();
		$data['success']=true;
		exit(json_encode($data));
	}
	
	public function upload(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->rootPath  = "data/upload/";
		$upload->maxSize = 1000 * 1024 * 1024 ;// 设置附件上传大小
		//$upload->exts = array('');// 设置附件上传类型
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
	
	
	public function download(){
		$id = intval(I("get.id"));
		$archive_file=D('ArchiveFile')->where('file_id='.$id)->find();
		if(!$archive_file){
			$this->error("文件不存在!");
		}
		
		if(!file_exists($archive_file['address'])){
			$this->error("文件已被删除!");
		}
		$file=$archive_file['address'];
		$fp=fopen($file,"r");
		$file_size=filesize($file);
		//下载文件需要用到的头
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length:".$file_size);
		Header("Content-Disposition: attachment; filename=".$archive_file['name']);
		$buffer=1024;
		$file_count=0;
		//向浏览器返回数据
		while(!feof($fp) && $file_count<$file_size){
			$file_con=fread($fp,$buffer);
			$file_count+=$buffer;
			echo $file_con;
		}
		fclose($fp);
		
	}
}