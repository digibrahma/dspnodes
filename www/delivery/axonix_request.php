<?php

require_once 'axonix.php';
require_once '../../init.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/Useragent.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/Browser.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/Domain.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/Language.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/ConnectionType.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/InApp.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Client/Os.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Time/Hour.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Time/Day.delivery.php';
require_once MAX_PATH . '/plugins/deliveryLimitations/Time/Date.delivery.php';;
$DSP_NAME_TO_ID['axonix'] = 7;
$cur_date = date('Y-m-d H:i:s');

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
	

	$queryString = array("testbid"=>"bid");
		
		http_build_query($queryString);
	
		if(!isset( $HTTP_RAW_POST_DATA)) 
		{
			$HTTP_RAW_POST_DATA =file_get_contents( 'php://input' );
		}

	$jsonStr = $HTTP_RAW_POST_DATA;
	
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
				
				
				$GLOBALS['_MAX']['CONF']['request_info']['bid_id'] = $bid_req_id;
				
				$GLOBALS['_MAX']['CLIENT']['ua'] = $req_arr['device']['ua'];
				$GLOBALS['_MAX']['CLIENT']['os'] = $req_arr['device']['os'];
				$GLOBALS['_MAX']['CLIENT']['osv'] = $req_arr['device']['osv'];
				$GLOBALS['_MAX']['CLIENT']['connectiontype'] = $req_arr['device']['connectiontype'];
				if(!empty($app_arr)) {
					$GLOBALS['_MAX']['CLIENT']['inapp'] = 1;	
				} else{
					$GLOBALS['_MAX']['CLIENT']['inapp'] = 0;	
				}
				
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


			$fetch_dsp['id']=$DSP_NAME_TO_ID[strtolower($_REQUEST['dsp'])];
		

			if(!empty($fetch_dsp['id']))
			{

			 
			$requery = $cur_date."|!|".$at."|!|".$fetch_dsp['id']."|!|".$con_type."|!|".$dev_type."|!|".$dgc."|!|".$geo_lat."|!|".$geo_lon."|!|".$geo_type."|!|".$dev_ip."|!|".$dev_js."|!|".$dev_make."|!|".$dev_model."|!|".$dev_os."|!|".$dev_ua."|!|".$ext_udi."|!|".$b_id."|!|".$b_type."|!|".$b_height."|!|".$b_mime."', 0, '".$b_width."|!|".$bid_floor."|!|".$display_manager."|!|".$bid."|!|".$scat."|!|".$s_domain."|!|".$s_id."|!|".$s_name."|!|".$s_p_id."|!|".$gender."|!|".$dob."|!|".$req_arr['device']['ua'];
			error_log($requery.PHP_EOL,3,"../../logs/dsp/dsp.".$fetch_dsp['id'].".".date('Y-m-d_H').".log");

			}
		}

	if(!empty($fetch_dsp['id']))
	{
	//error_log(print_r($data,true).PHP_EOL,3,"error.log");
	//error_log('\n',3,"error.log");
	$arr_serialize = serialize($data);

	$output=axonix_adprocessing($arr_serialize,$fetch_dsp['id']);
	//error_log(count($output).PHP_EOL,3,"error.log");
	}
	
if(strcasecmp($output['adtype'],'web')==0)
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
