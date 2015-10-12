<?php

require_once 'axonix.php';
require_once '../../init.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
	

	$queryString = array("testbid"=>"bid");
		
		http_build_query($queryString);
	
		if(!isset( $HTTP_RAW_POST_DATA)) 
		{
			$HTTP_RAW_POST_DATA =file_get_contents( 'php://input' );
		}

	$jsonStr = $HTTP_RAW_POST_DATA;
	error_log('['.date('d-m-Y H:i:s')."]",3,"../../logs/".date('d-m-Y')."_axonix_data.log");
	error_log(PHP_EOL,3,"../../logs/".date('d-m-Y')."_axonix_data.log");
	error_log(print_r($jsonStr,true),3,"../../logs/".date('d-m-Y')."_axonix_data.log");
	error_log(PHP_EOL,3,"../../logs/".date('d-m-Y')."_axonix_data.log");
/*	
	$jsonStr='{
"id":"c68a9bf31b794d35bb1bdbea45cd5e0bnghg4",
"app":{
"content":{
"keywords":"Test, Testing"
},
"id":"84058",
"cat":[
"IAB19"
],
"keywords":"Test, Testing ",
"name":"Sample Application",
"bundle":"123456",
"ver":"3.1"
},
"tmax":100,
"imp":[
{
"id":"4a3d2e45-77d6-45ca-8b89-34d3722fb4ae",
"bidfloor":0.3,
"instl":0,
"bidfloorcur":"USD",
"ext":{
"mobclix":{
"itunes_categories":"Technology & Computing"
}
},
"banner":{
"h":50,
"w":300,
"api":[
3
]
}
}
],
"device":{
"os":"ios",
"model":"iPhone",
"geo":{
"region":"MO",
"zip":"65109",
"country":"USA",
"city":"Jefferson City"
},
"osv":"7.1.2",
"dnt":0,
"ext":{
"idfa":"569FBC91-FAFB-4874-AE06-76F037B6E760"
},
"ip":"71.50.19.253",
"didsha1":null,
"connectiontype":2,
"dpidsha1":"629850f3bdcf4a91edbcd2afbe14548b03c68255",
"ua":"Mozilla/5.0 (iPhone; CPU iPhone OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Mobile/11D257",
"carrier":"311-480",
"devicetype":1,
"language":null,
"make":"Apple"
},
"user":{

}
}';	
*/
		
		$req_arr = json_decode($jsonStr, true);
		//error_log(print_r($req_arr,true),3,"error.log");
		//error_log('\ts',3,"error.log");
		$data	='';
		if(!empty($req_arr))
		{
				
					//error_log('\t',3,"error.log");
				// Auction Type
				if($req_arr['at']!=0 || $req_arr['at']!='')
				{
					$data['at'] 	= $req_arr['at'];
					
				} else { $data['at'] = 0; }
			
				//Bid request Id
				if($req_arr['id']!='')
				{
					$data['id']	= $req_arr['id'];
				} else { $data['id'] = ''; }
			
				// Device Array
				if(!empty($req_arr['device']))
				{
					$data['device'] = $req_arr['device'];
				} else { $data['device'] = array(); }
			
				// IMP Array
				if(!empty($req_arr['imp']))
				{
					$data['imp'] = $req_arr['imp'];
				} else { $data['imp'] = array(); }
			
				// Site Array
				if(!empty($req_arr['site']))
				{
					$data['site'] = $req_arr['site'];
				} else { $data['site'] = array(); }
			
				// App Array
				if(!empty($req_arr['app']))
				{
					$data['app'] = $req_arr['app'];
				} else { $data['app'] = array(); }
			
				// User Array
				if(!empty($req_arr['user']))
				{
					$user_arr = $req_arr['user'];
				} else { $user_arr = array(); }
			
				// Ext Array
				if(!empty($req_arr['ext']))
				{
					$data['user'] = $req_arr['user'];
				} else { $data['user'] = array(); }
			
				// tmax Array
				if(!empty($req_arr['tmax']))
				{
					$data['tmax'] = $req_arr['tmax'];
				} else { $data['tmax'] = array(); }
			
				// wseat Array
				if(!empty($req_arr['wseat']))
				{
					$$data['wseat'] = $req_arr['wseat'];
				} else { $data['wseat'] = array(); }
			
				// allimps Value - Flag to indicate whether Exchange can verify that all impressions

				if(!empty($req_arr['allimps']))
				{
					$data['allimps'] = $req_arr['allimps'];
				} else { $data['allimps'] = 0; }
			
				// Cur Array - Array of allowed currencies for bids on this bid request using ISO-4217 alphabetic codes.

				if(!empty($req_arr['cur']))
				{
					$data['cur'] = $req_arr['cur'];
				} else { $data['cur'] = array(); }
			
				// bcat Array - Blocked Advertiser Categories.
				if(!empty($req_arr['bcat']))
				{
					$data['bcat'] = $req_arr['bcat'];
				} else { $data['bcat'] = array(); }
			
				// badv Array - Array of strings of blocked top-level domains of advertisers.
				if(!empty($req_arr['badv']))
				{
					$data['badv']= $req_arr['badv'];
				} else { $data['badv'] = array(); }
			
			/*$data['at'] = $at;
				$data['id'] = $bid_req_id;
				$data['device'] = $device_arr;
				$data['imp'] = $imp_arr;
				$data['site'] = $site_arr;
				$data['app'] =  $app_arr;
				$data['user'] = $user_arr;
				$data['ext'] = $ext_arr;
				$data['tmax'] = $tmax_arr;
				$data['wseat'] = $wseat_arr;
				$data['allimps'] = $allimps;
				$data['cur'] = $cur_arr;
				$data['bcat'] = $bcat_arr; /**/
				
								// Inserting informations to table Smaato request
				$con_type = $req_arr['device']['connectiontype'];
				$dev_type = $req_arr['device']['devicetype'];
				$geo_lat = $req_arr['device']['geo']['lat'];
				$geo_lon= $req_arr['device']['geo']['lon'];
				$geo_type= $req_arr['device']['geo']['type'];
				$dev_ip 	= $req_arr['device']['ip'];
					$dev_js 	= $req_arr['device']['js'];
				$dev_make 	= $req_arr['device']['make'];
				$dev_model = @mysql_real_escape_string($req_arr['device']['model']);
				$dev_os		=$req_arr['device']['os'];
				$dev_ua 	= @mysql_real_escape_string($req_arr['device']['ua']);
			
				if(!empty($req_arr['ext']['udi']))
				{
					$ext_udi = $req_arr['ext']['udi'];
				} else {$ext_udi='';}
				$b_id 	= $req_arr['id'];
				if(!empty($req_arr['imp']))
				{
					foreach($req_arr['imp'] as $imp)
					{
						if(!empty($imp['banner']['btype']))
						{
							foreach($imp['banner']['btype'] as $btype)
							{
								$ban_type[] = $btype;
							}
						}
						$b_type = @implode('|',$ban_type);
						$b_height = $imp['banner']['h'];
						$b_width = $imp['banner']['w'];
						if(!empty($imp['banner']['mimes']))
						{
							foreach($imp['banner']['mimes'] as $bmime)
							{
								$ban_mime[] = $bmime;
							}
						}
						$b_mime = @implode('|',$ban_mime);
						$bid_floor = $imp['bidfloor'];
						$display_manager = $imp['displaymanager'];
						$bid = $imp['id'];
					}
				}
				if(!empty($req_arr['site']['cat']))
				{
					foreach($req_arr['site']['cat'] as $cat)
					{
						$s_cat[] = $cat;
					}
				}
				$scat = @implode('|',$s_cat);
				$s_domain = $req_arr['site']['domain'];
				$s_id = $req_arr['site']['id'];
				$s_name = @mysql_real_escape_string($req_arr['site']['name']);
				$s_p_id = $req_arr['site']['publisher']['id'];
				$gender = $req_arr['user']['gender'];
				$dob = $req_arr['user']['yob'];
				$dgc=$req_arr['device']['geo']['country'];
				$impbp='0';


			$fetch_dsp=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select id from rv_dj_dsp where dsp_portal_name='".$_REQUEST['dsp']."' and  status='1'"));
			$cur_date = date('Y-m-d H:i:s');
		

			if(!empty($fetch_dsp['id']))
			{

			 OA_Dal_Delivery_query("INSERT INTO  `{$table_prefix}dj_axonix_bid_request` (
				`datetime` ,
				`at` ,
				`exchange_id`,
				`device_connectiontype` ,
				`device_devicetype` ,
				`device_geo_country` ,
				`device_geo_latitude` ,
				`device_geo_longitude` ,
				`device_geo_type` ,
				`device_ip` ,
				`device_js` ,
				`device_make` ,
				`device_model` ,
				`device_os` ,
				`device_ua` ,
				`ext_udi` ,
				`bid_request_id` ,
				`imp_banner_type` ,
				`imp_banner_height` ,
				`imp_banner_mimes` ,
				`imp_banner_position` ,
				`imp_banner_width` ,
				`imp_bidfloor` ,
				`imp_displaymanager` ,
				`imp_id` ,
				`site_category` ,
				`site_domain` ,
				`site_id` ,
				`site_name` ,
				`publisher_id` ,
				`user_gender` ,
				`user_yob`
				)
				VALUES (
				'".$cur_date."', '".$at."', '".$fetch_dsp['id']."','".$con_type."', '".$dev_type."', '".$dgc."', '".$geo_lat."', '".$geo_lon."', '".$geo_type."', '".$dev_ip."', '".$dev_js."', '".$dev_make."', '".$dev_model."', '".$dev_os."', '".$dev_ua."', '".$ext_udi."', '".$b_id."', '".$b_type."', '".$b_height."', '".$b_mime."', 0, '".$b_width."', '".$bid_floor."', '".$display_manager."', '".$bid."', '".$scat."', '".$s_domain."', '".$s_id."', '".$s_name."', '".$s_p_id."', '".$gender."', '".$dob."')"); 

			}
		}

	if(!empty($fetch_dsp['id']))
	{
	//error_log(print_r($data,true),3,"error.log");
	//error_log('\n',3,"error.log");
	$arr_serialize = serialize($data);

	$output=axonix_adprocessing($arr_serialize,$fetch_dsp['id']);

	}
	
if($output['adtype']=='web')
	{
	
		$djax_adm="<a href='".$output['click_url']."' target='_blank'><img src='".$output['beacon_url']."'  style='display:none' alt='' /><img src='".$output['image_url']."' width='".$output['width']."' height='".$output['height']."' alt=''/></a>";
	
	}
	else
	{
		header("HTTP/1.0 204");	
    }	
    

/********** bid response **********/

	if($_GET['testbid']!="nobid")
	{

	$value=array(
	'id'		=>	$output['id'],
	'seatbid'	=>	array(
	array(
	'bid'	=>	array(
				array('id'=>$output['bid'],
					'impid'		=>	$output['impid'],
					'price'		=>	$output['price'],
					'adid'		=>	$output['adid'],
					'nurl'		=>	$output['nurl'],
					'adm'		=>	$djax_adm,
					'adomain'	=>	array($output['adomain']),
					'iurl'		=>	$output['image_url'],
					'cid'		=>	$output['cid'],
					'crid'		=>	$output['crid'])),
				'seat'	=>	$output['seat'])),
				'bidid'	=>	$output['bidid']
						);

		 
	}
	/********** empty bid response **********/
	else
	{
		header("HTTP/1.0 204");	
	}
	
	/********** Price is not zero **********/
	if($output['price']!='0')
	{
		
		$response 	= json_encode($value);
		
		print_r($response);
	}
	/********** Price is zero **********/
	else
	{	
		if(!function_exists('http_response_code'))
		{			
			function http_response_code()
			{					
				static $code 	= 	204;

				if($newcode !== NULL)
				{
					header('X-PHP-Response-Code: '.$newcode, true, $newcode);
					if(!headers_sent())
						$code 	= 	$newcode;
				} 					
					 
				return $code;
			}
		}
		
		$resonse	=	http_response_code();	
		
		print_r($resonse);			
	
	}

	mysql_close($con);

	function insertstring($string, $slash, $pos) 
	{
		return str_replace($pos, $pos.$slash ,$string);
	}

@mysql_close($GLOBALS['_MAX']['ADMIN_DB_LINK']);
