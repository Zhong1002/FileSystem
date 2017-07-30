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

