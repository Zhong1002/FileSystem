<?php
namespace Util\Controller;

class FileManager{
	const PERSON_ROOT='data/upload/person/';
	const COLLEGE_ROOT='data/upload/college/';
	
	private $root="";
	
	function __construct($user_id=0){
		if($user_id){
			if(!is_dir(self::PERSON_ROOT.$user_id)){
				mkdir(self::PERSON_ROOT.$user_id,0777,true);
			}
			$this->root=self::PERSON_ROOT.$user_id.'/';
		}
		else{
			$this->root=self::COLLEGE_ROOT;
		}
	}
	
	public function person_save($archive,$file_path){
		$type_address='';
		$this->get_type_address($archive['type_id'],$type_address);
		$save_path=$this->root.$type_address.$archive['archive_id'].'/';
		if(!is_dir($save_path)){
			mkdir($save_path,0777,true);
		}
		
		$res=rename($file_path,$save_path.$this->get_file_name($file_path));
		return '/'.$save_path.$this->get_file_name($file_path);
	}
	
	public function get_type_address($type_id, &$str_address){
		$type=M('ArchiveType')->where('id='.$type_id)->find();
		$str_address=$type['type_sn'].'/'.$str_address;
		if($type['parentid']!=0){
			$this->get_type_address($type['parentid'],$str_address);
		}
	}
	
	private function get_file_name($file_path){
		$arr=explode('/', $file_path);
		return $arr[count($arr)-1];
	}
}