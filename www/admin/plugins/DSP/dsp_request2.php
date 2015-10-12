<?php

$start = round(microtime(true) * 1000);

require_once 'dsp2.php';

require_once '../../../../init.php';

require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';


/*
$con = mysql_pconnect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
*/
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{		 
		$queryString = array("testbid"=>"bid");
	
	
		http_build_query($queryString);
	
		if(!isset( $HTTP_RAW_POST_DATA)) 
		{
			$HTTP_RAW_POST_DATA =file_get_contents( 'php://input' );
		}

		//$jsonStr = $HTTP_RAW_POST_DATA;
		

 $jsonStr = '{
  "at":2,
  "device":{
    "connectiontype":0,
    "devicetype":1,
    "geo":{
      "lat":42.357777,
      "lon":-71.06167,
      "type":1
    },
    "ip":"201.252.0.0",
    "js":0,
    "make":"Apple",
    "model":"iPhone",
    "os":"iOS",
    "osv":"1.2.3",
    "ua":"Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16"
  },
  "ext":{
    "coppa":0,
    "operaminibrowser":0,
    "udi":{ }
  },
  "id":"Cx4PI9q6Al",
  "imp":[
    {
      "banner":{
        "btype":[
          1,
          3
        ],
        "h":50,
        "mimes":[
          "image/gif",
          "image/jpeg",
          "image/png"
        ],
        "w":300
      },
      "displaymanager":"SOMA",
      "id":"1",
      "instl":0
    }
  ],
  "site":{
    "cat":[
      "IAB1"
    ],
    "domain":"www.jacksbar.co.uk",
    "id":"0",
    "name":"Jack Rabbit Slims",
    "publisher":{
      "id":"6"
    }
  },
  "user":{
    "gender":"M",
    "yob":1981
  }
}';
	
	

		$req_arr = json_decode($jsonStr, true);
		error_log(print_r($req_arr,true),3,"error.log");
	error_log("\n",3,"error.log");

			if(!empty($req_arr))
		{
				// Auction Type
				if($req_arr['at']!=0 || $req_arr['at']!='')
				{
					$at 	= $req_arr['at'];
				} else { $at = 0; }
			
				//Bid request Id
				if($req_arr['id']!='')
				{
					$bid_req_id	= $req_arr['id'];
				} else { $bid_req_id = ''; }
			
				// Device Array
				if(!empty($req_arr['device']))
				{
					$device_arr = $req_arr['device'];
				} else { $device_arr = array(); }
			
				// IMP Array
				if(!empty($req_arr['imp']))
				{
					$imp_arr = $req_arr['imp'];
				} else { $imp_arr = array(); }
			
				// Site Array
				if(!empty($req_arr['site']))
				{
					$site_arr = $req_arr['site'];
				} else { $site_arr = array(); }
			
				// App Array
				if(!empty($req_arr['app']))
				{
					$app_arr = $req_arr['app'];
				} else { $app_arr = array(); }
			
				// User Array
				if(!empty($req_arr['user']))
				{
					$user_arr = $req_arr['user'];
				} else { $user_arr = array(); }
			
				// Ext Array
				if(!empty($req_arr['ext']))
				{
					$ext_arr = $req_arr['user'];
				} else { $ext_arr = array(); }
			
				// tmax Array
				if(!empty($req_arr['tmax']))
				{
					$tmax_arr = $req_arr['tmax'];
				} else { $tmax_arr = array(); }
			
				// wseat Array
				if(!empty($req_arr['wseat']))
				{
					$wseat_arr = $req_arr['wseat'];
				} else { $wseat_arr = array(); }
			
				// allimps Value - Flag to indicate whether Exchange can verify that all impressions

				if(!empty($req_arr['allimps']))
				{
					$allimps = $req_arr['allimps'];
				} else { $allimps = 0; }
			
				// Cur Array - Array of allowed currencies for bids on this bid request using ISO-4217 alphabetic codes.

				if(!empty($req_arr['cur']))
				{
					$cur_arr = $req_arr['cur'];
				} else { $cur_arr = array(); }
			
				// bcat Array - Blocked Advertiser Categories.
				if(!empty($req_arr['bcat']))
				{
					$bcat_arr = $req_arr['bcat'];
				} else { $bcat_arr = array(); }
			
				// badv Array - Array of strings of blocked top-level domains of advertisers.
				if(!empty($req_arr['badv']))
				{
					$badv_arr = $req_arr['badv'];
				} else { $badv_arr = array(); }
			
				$data['at'] = $at;
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
				$data['bcat'] = $bcat_arr;
				$data['badv'] = $badv_arr;

				// Inserting informations to table Smaato request
				$con_type = $req_arr['device']['connectiontype'];
				$dev_type = $req_arr['device']['devicetype'];
				$geo_lat = $req_arr['device']['geo']['lat'];
				$geo_lon= $req_arr['device']['geo']['lon'];
				$geo_type= $req_arr['device']['geo']['type'];
				$dev_ip 	= $req_arr['device']['ip'];
				$dev_js 	= $req_arr['device']['js'];
				$dev_make 	= $req_arr['device']['make'];
				$dev_model = $req_arr['device']['model'];
				$dev_os		=$req_arr['device']['os'];
				$dev_ua 	= mysql_real_escape_string($req_arr['device']['ua']);
			
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
						$b_type = implode('|',$ban_type);
						$b_height = $imp['banner']['h'];
						$b_width = $imp['banner']['w'];
						if(!empty($imp['banner']['mimes']))
						{
							foreach($imp['banner']['mimes'] as $bmime)
							{
								$ban_mime[] = $bmime;
							}
						}
						$b_mime = implode('|',$ban_mime);
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
				$scat = implode('|',$s_cat);
				$s_domain = $req_arr['site']['domain'];
				$s_id = $req_arr['site']['id'];
				$s_name = mysql_real_escape_string($req_arr['site']['name']);
				$s_p_id = $req_arr['site']['publisher']['id'];
				$gender = $req_arr['user']['gender'];
				$dob = $req_arr['user']['yob'];
				$dgc=$req_arr['device']['geo']['country'];
				$impbp='0';


			$fetch_dsp=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select id from rv_dj_dsp where dsp_portal_name='".$_REQUEST['dsp']."' and  status='1'"));
			$cur_date = date('Y-m-d H:00:00');
		

			if(!empty($fetch_dsp['id']))
			{

			/* Log DSP request into database*/

				
			$requery=OA_Dal_Delivery_query("INSERT INTO  `{$table_prefix}dj_dsp_bid_request` (
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
				'".$cur_date."', '".$at."', '".$fetch_dsp['id']."','".$con_type."', '".$dev_type."', '".$dgc."', '".$geo_lat."', '".$geo_lon."', '".$geo_type."', '".$dev_ip."', '".$dev_js."', '".$dev_make."', '".$dev_model."', '".$dev_os."', '".$dev_ua."', '".$ext_udi."', '".$b_id."', '".$b_type."', '".$b_height."', '".$b_mime."', 0, '".$b_width."', '".$bid_floor."', '".$display_manager."', '".$bid."', '".$scat."', '".$s_domain."', '".$s_id."', '".$s_name."', '".$s_p_id."', '".$gender."', '".$dob."')") or error_log(mysql_error(), 3, '../../../../var/query.log'); 
	
			
				
			}
		} 
}


	if(!empty($fetch_dsp['id']))
	{
	/*Serialize the bid request into adserver*/

	$arr_serialize = serialize($data);

	$output=dsp_adprocessing($arr_serialize,$fetch_dsp['id']);

	}

	if($output['adtype']=='web')
	{

		$djax_adm ='<?xml version=\"1.0\" ?>';
		$djax_adm.='<ad xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"smaato_ad_v0.9.xsd\" modelVersion=\"0.9\">';
		$djax_adm.='
			<imageAd>
				<clickUrl>'.stripcslashes($output['click_url']).'</clickUrl>
				<imgUrl>'.stripcslashes($output['image_url']).'</imgUrl>
				<width>'.stripcslashes($output['width']).'</width>
				<height>'.stripcslashes($output['height']).'</height>
				<additionalText></additionalText>
				<beacons>
					<beacon>'.stripcslashes($output['beacon_url']).'</beacon>
				</beacons>		
			</imageAd>
		</ad>';
	}
	else if($output['adtype']=='txt')
	{
		$djax_adm ='<?xml version=\"1.0\" ?>';
		$djax_adm.='<ad xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"smaato_ad_v0.9.xsd\" modelVersion=\"0.9\">';
		$djax_adm.='
			<textAd>
				<clickText>'.stripcslashes($output['additional_text']).'</clickText>
				<clickUrl>'.stripcslashes($output['click_url']).'</clickUrl>
				<additionalText></additionalText>
				<beacons>
					<beacon>'.stripcslashes($output['beacon_url']).'</beacon>
				</beacons>		
			</textAd>
		</ad>';
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
					'iurl'		=>	null,
					'cid'		=>	$output['cid'],
					'crid'		=>	$output['crid'],
					'attr'		=>	array(),
					'ext'		=>	null)),
				'seat'	=>	$output['seat'],
				'group'	=>	'0')),
				'bidid'	=>	$output['bidid'],
				'customdata'=>	null,
				'cur'	=>	'USD',
				'ext'	=>	null
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

		$response 	= 	json_encode($value);
	
	$end = round(microtime(true) * 1000);
	$diss = $end-$start;
	//error_log("Start : ".$start." ------ End : ".$end."&Diff=".$diss."\n",3,"error.log");
	
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


	function insertstring($string, $slash, $pos) 
	{
		return str_replace($pos, $pos.$slash ,$string);
	}


?>
