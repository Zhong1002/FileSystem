<?php

namespace Common\Model;
use Think\Model\ViewModel;
class ArchiveViewModel extends ViewModel {
	public $viewFields = array(
		'Archive'	=>	array(
			'archive_id',
			'user_id',
			'type_id',
			'archive_sn',
			'name',
			'date_time',
			'share_type',
			'check_user',
			'status',
			'create_time',
			'update_time',
		    '_type' => 'LEFT' 
		),
		'Users'	=>	array(
			'user_nicename',
			'user_tel',
			'user_email',
			'office_tel',
			'_on' => 'Archive.user_id=Users.id',
		    '_type' => 'LEFT'
		),
		'ArchiveType'=>array(
			'name'=>'type_name',
			'type_sn',
			'_on' => 'ArchiveType.id=Archive.type_id'
		)
	);



	
	
}

?>