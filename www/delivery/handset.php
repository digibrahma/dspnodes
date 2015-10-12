<?php

//die("hi");
$_query = mysql_query("select * from oxm_terawurfl") or die(mysql_error());

$_tera_path = '';


	if(mysql_num_rows($_query)) {

		$_row = mysql_fetch_assoc($_query);

		$_tera_path = $_row['terawurfl_path'];

	} 


$_tera_path .= "/webservice.php";

define("M_WURFL", $_tera_path);

function changeZone($zoneid=0)
{

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select3:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
global $keycode, $mobileSize, $errorM;
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'SonyEricssonK700i/R2AC SEMC-Browser/4.0.2 Profile/MIDP-2.0 Configuration/CLDC-1.1' ;

	$all_values = getCapabilityValues(M_WURFL."?ua=".urlencode($ua));
	$common = $all_values->device;
	$all = $common->capability;

	$width	=	objectToArray($all_values);

	$os = $all[0]['value'];   //device OS

	$model_name = $all[1]['value'];  //device Model

	$brand_name = $all[2]['value'];  //device Brand

	$widthx = $width['device']['capability']['131']['@attributes']['value']; //device width

	$heightx = $width['device']['capability']['130']['@attributes']['value']; //device height

        $smartmobile = $all[8]['value'];
        
$MR = array();
$width = array();
$height = array();


$query = mysql_query("select * from oxm_mobilezonesize");
	while($row = mysql_fetch_assoc($query))
	{
			$width[$row['zonecategory']] = $row['width'];
			$height[$row['zonecategory']] = $row['height'];
			
	}


$mobileSize['extralarge']['width'] 	= $width[1];
$mobileSize['extralarge']['height']	= $height[1];
$mobileSize['large']['width'] 		= $width[2];
$mobileSize['large']['height'] 		= $height[2];
$mobileSize['medium']['width'] 		= $width[3];
$mobileSize['medium']['height'] 	= $height[3];
$mobileSize['small']['width'] 		= $width[4];
$mobileSize['small']['height'] 		= $height[4];


$djsize=array(1=>$width[1],2=>$width[2],3=>$width[3],4=>$width[4]);
$adsizes=array();

	$zonequery = mysql_query("select * from oxm_mobilezones where masterzoneid=".$zoneid." ");
        $zonerow = mysql_fetch_assoc($zonequery);

	if(mysql_num_rows($zonequery) > 0)
	{

		 $MR['width']  = $widthx;     //'216';//$mobileInfo['display']['max_image_width'];
		 $MR['height'] = $heightx;    //'36';$mobileInfo['display']['max_image_height'];
		$mz=4;

		foreach($djsize as $key=>$adsize)
		{
						if($adsize > $MR['width']) 
						{
							continue;
						}
						else
						{
						$adsizes[]=$adsize;
						}
		}

		rsort($adsizes);

		$getzones = mysql_fetch_array(mysql_query("select * from {$table_prefix}zones where masterzone=".$zoneid." and width='".$adsizes[0]."'"));
		$succes_zoneid=(!empty($getzones))?$getzones['zoneid']:$zoneid;
       
		return $succes_zoneid;

	}
	else
	{
	 $errorM .= 'Error:Mobile Zone';
	}	

	return $zoneid; #return zoneid as passed if Handset Detection account is not present in OpenX for the User

}



function getCapabilityValues($url){
	//echo($url);die("getCapabilityValues");
	$rfd = fopen($url, 'r');
	//print_r($rfd);
	stream_set_blocking($rfd,true);
	stream_set_timeout($rfd, 20);  // 20-second timeout


	$data = stream_get_contents($rfd);
	$status = stream_get_meta_data($rfd);
	
	fclose($rfd);
	
	if($status['timed_out']){
			$xml = simplexml_load_file($url);
		}else{
			$xml = simplexml_load_string($data);
		}
return $xml;

}


function getVirtualCapabilityValues($url){
	//echo($url);die("askk");
	$rfd = fopen($url, 'r');
	//print_r($rfd);
	stream_set_blocking($rfd,true);
	stream_set_timeout($rfd, 20);  // 20-second timeout


	$data = stream_get_contents($rfd);//print_r($data);die("as..l,23,2hh");
	$status = stream_get_meta_data($rfd);
	
	fclose($rfd);
	
	if($status['timed_out']){
			$xml = simplexml_load_file($url);
		}else{
			$xml = simplexml_load_string($data);
		}
return $xml;

}
?>
