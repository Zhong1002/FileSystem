<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomeBaseController; 
/**
 * 首页
 */
class IndexController extends HomeBaseController {
	
    //首页
	public function index() {
    	//$this->display(":index");
		//echo "明之阳科技版权所有 copyright2012";
		//$this->redirect(U('admin/index/index'));
		//header("Location: ../index.php?g=admin");
		$_SESSION['adminlogin'] = 1;
		header("Location: ../index.php?g=admin");
    }   

}


