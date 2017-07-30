<?php

namespace Common\Model;
use Think\Model\ViewModel;
class ArchiveTypeViewModel extends ViewModel {
	public $viewFields = array(
		'ArchiveType'	=>	array(
			'id',
			'type_sn',
			'name',
			'content',
			'parentid',
			'role_id',
			'status',
			'create_time',
			'update_time',
		    '_type' => 'LEFT' 
		),
		'Role'	=>	array(
			'name'=>'role_name',
			'_on' => 'ArchiveType.role_id=Role.id'
		)
	);



	
	
}

?>