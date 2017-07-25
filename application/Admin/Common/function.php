<?php

function sp_get_url_route(){
	$apps=sp_scan_dir(SPAPP."*",GLOB_ONLYDIR);
	$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
	$routes=array();
	foreach ($apps as $a){
	
		if(is_dir(SPAPP.$a)){
			if(!(strpos($a, ".") === 0)){
				$navfile=SPAPP.$a."/nav.php";
				$app=$a;
				if(file_exists($navfile)){
					$navgeturls=include $navfile;
					foreach ($navgeturls as $url){
						//echo U("$app/$url");
						$nav= file_get_contents($host.U("$app/$url"));
						$nav=json_decode($nav,true);
						if(!empty($nav) && isset($nav['urlrule'])){
							if(!is_array($nav['urlrule']['param'])){
								$params=$nav['urlrule']['param'];
								$params=explode(",", $params);
							}
							sort($params);
							$param="";
							foreach($params as $p){
								$param.=":$p/";
							}
							
							$routes[strtolower($nav['urlrule']['action'])."/".$param]=$nav['urlrule']['action'];
						}
					}
				}
					
			}
		}
	}
	
	return $routes;
}


/**
 * 根据时间戳返回这个月的第一天
 * @param number $timestmp
 * @return number
 */
function sp_api_get_first_day_of_month($timestmp=0){
	if($timestmp){
		return strtotime(date('Y-m-01 00:00:00', $timestmp));
	}
	return strtotime(date('Y-m-01 00:00:00', strtotime('now')));
}


/**
 * 根据时间戳返回这个月的最后一天(下个月1号0时0分0秒)
 * @param number $timestmp
 * @return number
 */
function sp_api_get_last_day_of_month($timestmp=0){
	if($timestmp){
		$firstday=date('Y-m-01 00:00:00', $timestmp);
		return strtotime("$firstday +1 month ");
	}
	$firstday=date('Y-m-01 00:00:00', strtotime('now'));
	return strtotime("$firstday +1 month ");
}


/**
 * 根据时间戳返回今天0点0分0秒
 * @param number $timestmp
 * @return number
 */
function sp_api_get_first_time_of_day($timestmp=0){
	if($timestmp){
		return strtotime(date('Y-m-d 00:00:00', $timestmp));
	}
	return strtotime(date('Y-m-d 00:00:00', strtotime('now')));
}


/**
 * 根据时间戳返回这一天最后一秒的时间(第二天0时0分0秒)
 * @param number $timestmp
 * @return number
 */
function sp_api_get_last_time_of_day($timestmp=0){
	if($timestmp){
		$firstday=date('Y-m-d 00:00:00', $timestmp);
		return strtotime("$firstday +1 day ");
	}
	$firstday=date('Y-m-d 00:00:00', strtotime('now'));
	return strtotime("$firstday +1 day ");
}
/**
 * 得到行政区id
 * @param unknown $discrict_name
 */
function sp_api_get_discrict_id($discrict_name){
	switch ($discrict_name){
		case "龙岗区":
			return 1;
		case "南山区":
			return 2;
		case "福田区":
			return 3;
		case "罗湖区":
			return 4;
		case "盐田区":
			return 5;
		case "宝安区":
			return 6;
		case "龙华区":
			return 7;
		case "坪山区":
			return 8;
		case "光明新区":
			return 9;
		case "大鹏新区":
			return 10;
	}
}
	
/**
 * 得到行政区id
 * @param unknown $discrict_name
 */
function sp_api_get_discrict_name($discrict_id){
	switch ($discrict_id){
		case 1:
			return "龙岗区";
		case 2:
			return "南山区";
		case 3:
			return "福田区";
		case 4:
			return "罗湖区";
		case 5:
			return "盐田区";
		case 6:
			return "宝安区";
		case 7:
			return "龙华区";
		case 8:
			return "坪山区";
		case 9:
			return "光明新区";
		case 10:
			return "大鹏新区";
	}
}

/**
 * 得到班级id
 * @param unknown $discrict_name
 */
function sp_api_get_grade_id($discrict_name){
	switch ($discrict_name){
		case "小班":
			return 1;
		case "中班":
			return 2;
		case "大班":
			return 3;
		
	}
}

/**
 * 得到班级id
 * @param unknown $discrict_name
 */
function sp_api_get_grade_name($grade_id){
	switch ($grade_id){
		case 1:
			return "小班";
		case 2:
			return "中班";
		case 3:
			return "大班";
		case 0:
			return "";

	}
}

/**
 * 房源所属id
 * @param unknown $discrict_name
 */
function sp_api_get_house_to_id($discrict_name){
	switch ($discrict_name){
		case "父母":
			return 1;
		case "祖父母":
			return 2;
		case "外祖父母":
			return 3;
		default:
			return 0;
				
	}
}
	/**
	 * 输出
	 * @param unknown $str
	 * @param unknown $status 1正常(黑色)
	 * 						  2错误(红色)
	 *                        3重点(蓝色)
	 */
 function  sp_api_out_put($str,$status=1){
		if($status==1){
			echo $str;
		}
		elseif($status==2){
			echo "<font color='red'>".$str."</font>";
		}
		else{
			echo "<font color='blue'>".$str."</font>";
		}
	
		echo "<br/>";
		ob_flush();
		flush();
}

function sp_api_get_term_name($no){
	if(substr($no,4)==02){
		$str=substr($no,0,4)."春季";
	}
	else{
		$str=substr($no,0,4)."秋季";
	}
}



