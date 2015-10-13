<?php

require_once '../../init.php';

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

function OX_Delivery_logMessage($message, $priority = 6)
{
$conf = $GLOBALS['_MAX']['CONF'];
if (empty($conf['deliveryLog']['enabled'])) return true;
$priorityLevel = is_numeric($conf['deliveryLog']['priority']) ? $conf['deliveryLog']['priority'] : 6;
if ($priority > $priorityLevel && empty($_REQUEST[$conf['var']['trace']])) { return true; }
error_log('[' . date('r') . "] {$conf['log']['ident']}-delivery-{$GLOBALS['_MAX']['thread_id']}: {$message}\n", 3, MAX_PATH . '/var/' . $conf['deliveryLog']['name']);
//OX_Delivery_Common_hook('logMessage', array($message, $priority));
return true;
}

function djax_getAd($adid,$id)
{
		//error_log('Test',3,"error.log");	
		$bid_floor=$GLOBALS['_MAX']['CONF']['request_info']['imp'][0]['bidfloor'];
		$width=	$GLOBALS['_MAX']['CONF']['request_info']['imp'][0]['banner']['w'];
		$height=$GLOBALS['_MAX']['CONF']['request_info']['imp'][0]['banner']['h'];
		$reqaddomain=$GLOBALS['_MAX']['CONF']['request_info']['badv'];
		$reqcat=$GLOBALS['_MAX']['CONF']['request_info']['bcat'];	
		$btype=$GLOBALS['_MAX']['CONF']['request_info']['imp'][0]['banner']['btype'];
			
		$cc="FIND_IN_SET(".$id.",c.dsp_portals)";
		
		$conf = $GLOBALS['_MAX']['CONF']['database'];
		$host=$conf['host'];
		$db=$conf['name'];	
		
		$database=$conf['name'];	
		$dbh = new PDO("mysql:host=$host;port=3306;dbname=$database", $conf['username'],  $conf['password']) or die(mysql_error());
		if($dbh)
		{
			//error_log('['.date('d-m-Y H:i:s').'] Woking ',3,"../../../../logs/pdo.log");
			//error_log(PHP_EOL,3,"../../../../logs/pdo.log");
		}
		else
		{
			error_log('['.date('d-m-Y H:i:s').'] axonix faild ',3,"../../../../logs/pdo.log");
			error_log(PHP_EOL,3,"../../../../logs/pdo.log");
		}	
		$sql = "call getAd('".$width."','".$height."','".$id."','web','".$reqaddomain."','".$reqcat."')";
		
		foreach ($dbh->query($sql) as $aAd)
		{
			//error_log(print_r($aAd,true),3,"error.log");
		if($bid_floor<=$aAd['revenue'])
		{	
			
			$aRows[$aAd['ad_id']] = $aAd;
			
		}
		else
		{
			$re_id	=	$GLOBALS['_MAX']['CONF']['request_info']['id'];		
			error_log('['.date('d-m-Y H:i:s').'] bidid=	'.$re_id.'	banner '.$aAd['name']." price is below requested price ".$bid_floor,3,'../../logs/204/axonix/'.date('d-m-Y')."_axonix_204.log");
					error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix_204.log");
		}
		//$aRows[$aAd['ad_id']] = $aAd;
		$dbh = null;	
		
		$GLOBALS['_MAX']['CONF'][$aAd['ad_id']]=$aRows;
		//error_log(print_r($aRows,true),3,"error.log");
		return $aRows;
}

		
	//$GLOBALS['_MAX']['CONF'][$aAd['ad_id']]=$aRows;
	
	return $aRows;

}

function axonix_adprocessing($requestparams,$id)
{
	//error_log(print_r($requestparams,true),3,"error.log");
	//error_log('\n',3,"error.log");
	$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

	$limitedcampaigns=array();

 	$request=array();

	$request_array=unserialize($requestparams);
	
	$GLOBALS['_MAX']['CONF']['request_info']=$request_array;
	//error_log(print_r($request_array,true),3,"error.log");
	//error_log('\n',3,"error.log");
	$djax_allads=djax_getAd($request_array,$id);
	//error_log(print_r($djax_allads,true),3,"error.log");
	//error_log('\n',3,"error.log");		
	$limitedcampaigns=_adSelectCheckCriteria($djax_allads);
	
	$finallimit=@array_intersect_key($djax_allads,$limitedcampaigns);
	 
	/*$results =@array_reduce($finallimit, function ($a, $b) {
    return @$a['revenue'] > $b['revenue'] ? $a : $b ;
	});*/

$results =	$finallimit[array_rand($finallimit,1)];
error_log('['.date('d-m-Y H:i:s').']',3,'../../../../logs/204/axonix/'.date('d-m-Y')."_axonix_res.log");
error_log(PHP_EOL,3,'../../../../logs/204/axonix/'.date('d-m-Y')."_axonix_res.log");
error_log(print_r($results,true),3,'../../../../logs/204/axonix/'.date('d-m-Y')."_axonix_res.log");
	
if($results['ad_id']!=0)
{
	
		$deliverypath=$GLOBALS['_MAX']['CONF']['webpath']['delivery'];
		$imagepath=$GLOBALS['_MAX']['CONF']['webpath']['images'];
		$adminpath=$GLOBALS['_MAX']['CONF']['webpath']['admin'];

		$clickurl='http://'.$deliverypath."/ck.php?oaparams=2__bannerid=".$results['ad_id']."__zoneid=0__cb={random}__oadest=";

		$beaconurl='http://'.$deliverypath."/lg.php?bannerid=".$results['ad_id']."&amp;campaignid=".$results['placement_id']."&amp;zoneid=0&amp;cb={random}";

		$imageurl='http://'.$imagepath."/".$results['filename'];			

		$cur_date  = 	date('Y-m-d H:i:s');
			
					//."' AND datetime='".$cur_date."'"
				$request_query 	= 	OA_Dal_Delivery_query("SELECT id FROM {$table_prefix}dj_axonix_bid_request WHERE bid_request_id='".$request_array['id']."'") ;
				$request_row 	= 	OA_Dal_Delivery_fetchAssoc($request_query);
				$requset_id 	= 	$request_row['id'];
								
				
		 OA_Dal_Delivery_query("INSERT INTO  
								`{$table_prefix}dj_axonix_response` (
									`datetime`,
									`requset_id`,
									`id` ,
									`imp_id` ,
									`imp_width` ,
									`imp_height` ,
									`seat` ,
									`floor_price` ,
									`advertiser_bid_price` ,
									`smaato_bid_price` ,
									`admin_rev` ,
									`adid` ,
									`bannerid` ,
									`campaign_id` ,
									`type`
								)
								VALUES (
									 '".$cur_date."',
									 '".$requset_id."',
									 '".$request_array['id']."',
									 '".$request_array['imp'][0]['id']."', 
									 '".$results['width']."', 
									 '".$results['height']."', 
									 '8a809449012f2f0744180791edfc0003', 
									 '".$GLOBALS['_MAX']['CONF']['request_info']['imp'][0]['bidfloor']."', 
									 '".$results['revenue']."',
									 '0',
									 '0', 
									 '".$results['ad_id']."', 
									 '".$results['ad_id']."', 
									 '".$results['placement_id']."', 
									 '".$request_array['device']['devicetype']."'
								)"); 
			
			
				$response_array= array(
			"id"=>$request_array['id'],
			"bid"=>"368986290101875502942021904441292",
			"impid"=>$request_array['imp'][0]['id'],
			"price"=>$results['revenue'],
			"adid"=>$results['ad_id'],
			"nurl"=> 'http://'.$deliverypath.'/axonix_winnotice.php?auctionId=${AUCTION_ID}&bidid=${AUCTION_BID_ID}&price=${AUCTION_PRICE}&impid=${AUCTION_IMP_ID}&seatid=${AUCTION_SEAT_ID}&adid=${AUCTION_AD_ID}&cur=${AUCTION_CURRENCY}',
			"click_url"=>$clickurl,
			"image_url"=>$imageurl,
			"additional_text"=>$results['bannertext'],
			"beacon_url"=>$beaconurl,
			"adomain"=>$_SERVER['HTTP_HOST'],
			"iurl"=>"",
			"cid"=>$results['placement_id'],
			"crid"=>$results['ad_id'],
			"attr"=>"",
			"ext"=>"",
			"tooltip"=>"",
			"seat"=>"8a809449012f2f0744180791edfc0003",
			"group"=>0,
			"bidid"=>$request_array['id'],
			"cur"=>"USD",
			"customdata"=>"",
			"ext"=>"",
			"width"=>$results['width'],
			"height"=>$results['height'],
			"adtype"=>$results['type']
		);	
			
	
return $response_array;

}
}

function _adSelectCheckCriteria($ads)
{
	
	$djax_response=array();
	if(count($ads)>0)
	{	$ip_limit	=	'';
			foreach($ads as $key => $value)
			{
				
				$iprange=iprange($value['placement_id']);
				$ip_limit	= $value['placement_id'];
				if(empty($iprange))
				{
					unset($ads[$key]);
				}		
					
				if($ads[$key] && !empty($value['compiledlimitation']))
				{		
					@eval('$result = (' . $value['compiledlimitation'] . ');');
					
					if(empty($result))
					{
						$result=0;
						unset($ads[$key]);
					}

				}
			
			/*	else // sep29
				{
					$djax_response[$key]=1;
				} */
				
			if($ads[$key])
				{
					$djax_response[$key]=1;
				}
			}
			if(count($djax_response)==0)
			{




				$re_id	=	$GLOBALS['_MAX']['CONF']['request_info']['id'];
				$request_ip=	$GLOBALS['_MAX']['CONF']['request_info']['device']['ip'];
				error_log('['.date('d-m-Y H:i:s').'] bidid= '.$re_id.' Request IP '.$request_ip." is not available in Campaign id ".$ip_limit." iprange",3,'../../logs/204/axonix/'.date('d-m-Y')."_axonix_204.log");
				error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix_204.log");	
/*
				error_log('['.date('d-m-Y H:i:s').'] bidid= '.$re_id.' Request IP '.$request_ip." is not available in Campaign id ".$ip_limit." iprange",3,'../../logs/204/axonix/'.date('d-m-Y')."_axonix1_204.log");
				error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix1_204.log");	*/
 

			}
	
	}
return $djax_response;

}

function iprange($placement_id)
{
	$ip		=	$GLOBALS['_MAX']['CONF']['request_info']['device']['ip'];

	$qryCount	=	OA_Dal_Delivery_query("SELECT ipranges as cc FROM rv_campaign_iprange WHERE campaignid = '".$placement_id."' ");
	$rowCount	=	mysql_fetch_row($qryCount);
	if(preg_replace('/\s+/', '', $rowCount[0])!="")
	{
		/*$qry	=	OA_Dal_Delivery_query("SELECT ipranges FROM rv_campaign_iprange WHERE campaignid = '".$placement_id."' ");
		$row=	mysql_fetch_row($qry);*/
		
		$temp	=	$rowCount[0];
		$flag	=	"0";
		
	//	$ipr="select ipaddress from djax_iprange  WHERE (INET_ATON('$ip') BETWEEN INET_ATON(`hostmin`) AND INET_ATON(`hostmax`)) and locid in($temp)";
		
		$qry1	=	OA_Dal_Delivery_query("select ipaddress from djax_iprange  WHERE (INET_ATON('$ip') BETWEEN INET_ATON(`hostmin`) AND INET_ATON(`hostmax`)) and locid in($temp)");
		$rowCounting	=	mysql_fetch_row($qry1);
		//print_r($rowCounting);
		if($rowCounting)
		{
					
			return true;
		}
		else
		{
			//echo "test";
			return false;
		}
	}
		else 
		{
			return true;
		}
		
}

function country($limitation, $op)
{
$paramName=$GLOBALS['_MAX']['CONF']['request_info']['device']['geo']['country'];
$fetchrows=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("SELECT value FROM `djax_targ_country` where iso_countycode_alpha3='".$paramName."'"));

$data = explode(',',$limitation);
$finaldetails=array_map('strtolower',$data);

if( $op=='=~' && in_array(strtolower($fetchrows['value']),$finaldetails))
{
return true;
}
elseif( $op=='!~' && !(in_array(strtolower($fetchrows['value']),$finaldetails)))
{
return true;
}
else
{
	
	$re_id	=	$GLOBALS['_MAX']['CONF']['request_info']['id'];
		error_log('['.date('d-m-Y H:i:s').'] bidid= '.$re_id.' Request country '.$paramName." is not matched",3,'../../logs/204/axonix/'.date('d-m-Y')."_axonix_204.log");
	error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix_204.log");

  


return false;
}
}


function os($limitation, $op)
{
$data = explode(',',$limitation);
$finaldetails=array_map('strtolower',$data);
$req_os	=	$GLOBALS['_MAX']['CONF']['request_info']['device']['os'];
if( $op=='=~' && in_array(strtolower($GLOBALS['_MAX']['CONF']['request_info']['device']['os']),$finaldetails))
{
return true;
}
elseif( $op=='!~' && !(in_array(strtolower($GLOBALS['_MAX']['CONF']['request_info']['device']['os']),$finaldetails)))
{
return true;
}
else
{
	$re_id	=	$GLOBALS['_MAX']['CONF']['request_info']['id'];
	error_log('['.date('d-m-Y H:i:s').'] bidid= '.$re_id.' Request Operating  system '.$req_os." is not matched",3,'../../logs/204/axonix/'.date('d-m-Y')."_axonix_204.log");
	error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix_204.log");
return false;
}

}

function model($limitation, $op)
{
$data = explode(',',$limitation);
$finaldetails=array_map('strtolower',$data);
$req_model	=	$GLOBALS['_MAX']['CONF']['request_info']['device']['model'];
if( $op=='=~' && in_array(strtolower($GLOBALS['_MAX']['CONF']['request_info']['device']['model']),$finaldetails))
{
return true;
}
elseif( $op=='!~' && !(in_array(strtolower($GLOBALS['_MAX']['CONF']['request_info']['device']['model']),$finaldetails)))
{
return true;
}
else
{
	$re_id	=	$GLOBALS['_MAX']['CONF']['request_info']['id'];
	error_log('['.date('d-m-Y H:i:s').'] bidid=	'.$re_id.'	Request model  '.$req_model." is not matched",3,'../../logs/204/axonix/'.date('d-m-Y')."_smaato_204.log");
	error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix_204.log");

return false;
}
}

function handset($limitation, $op, $aParams = array())
{
$data = explode(',',$limitation);
$finaldetails=array_map('strtolower',$data);
$make	= $GLOBALS['_MAX']['CONF']['request_info']['device']['make'];
if( $op=='=~' && in_array(strtolower($GLOBALS['_MAX']['CONF']['request_info']['device']['make']),$finaldetails))
{
return true;
}
elseif( $op=='!~' && !(in_array(strtolower($GLOBALS['_MAX']['CONF']['request_info']['device']['make']),$finaldetails)))
{
return true;
}
else
{
	$re_id	=	$GLOBALS['_MAX']['CONF']['request_info']['id'];
	error_log('['.date('d-m-Y H:i:s').'] bidid = '.$re_id.' Request handset  '.$make." is not matched",3,'../../logs/204/axonix/'.date('d-m-Y')."_smaato_204.log");
	error_log(PHP_EOL,3,"../../logs/204/axonix/".date('d-m-Y')."_axonix_204.log");
	return false;
}
	
}
