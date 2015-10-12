<?php

function pageRedirection($campId,$type="yes",$image="no"){
	require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';


$redirectionFlag=0; // Flag to Redirect

$campaignPK=$campId;

//$campaignPK=5;

/*************** Redirection for Country Starts **************/

// Getting Country Code
$userCountry=$GLOBALS['_MAX']['CLIENT_GEO']['country_code'];

// Selecting Country Primary Key as Comma Seperated String
$qry=OA_Dal_Delivery_query("select country_fk from rv_campaign_redirect where campaign_fk='".$campaignPK."'");
while($row=OA_Dal_Delivery_fetchAssoc($qry)){

//  Comma Seperated Country Primary Key(String to Array)
$countryArray = explode(',', $row['country_fk']);

// Looping or Parsing Array to get country Primary Key
	for($i=0;$i<count($countryArray);$i++){
		//  Geeting Country Code
		$result = OA_Dal_Delivery_query("SELECT country_code FROM oxm_country WHERE id = '".$countryArray[$i]."'");
		$row1 = mysql_fetch_row($result);
		
		// Checking Country Code with Actual Country
		if($row1[0]==$userCountry && $userCountry!="" && $userCountry!=" "){
	
		$redirectionFlag=1;
		}
		}

	
	}
/*************** Redirection for Country Ends **************/


/*************** Redirection for OS Starts **************/


$osType="";
// Selecting OS Primary Key as Comma Seperated String
$qry=OA_Dal_Delivery_query("select operating_system from rv_campaign_redirect where campaign_fk='".$campaignPK."'");
$optmise=0;
while($row=OA_Dal_Delivery_fetchAssoc($qry)){

//  Comma Seperated OS Primary Key(String to Array)
$osArray = explode(',', $row['operating_system']);

// Looping or Parsing Array to get OS Primary Key
	for($i=0;$i<count($osArray);$i++){
		
		if(preg_replace('/\s+/', '', $osArray[$i])!="")
		{
			
			if($optmise==0){
				// Getting OS Type
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'SonyEricssonK700i/R2AC SEMC-Browser/4.0.2 Profile/MIDP-2.0 Configuration/CLDC-1.1' ;
require_once 'handset.php';
$all_values = getCapabilityValues(M_WURFL."?ua=".urlencode($ua));
	$common = $all_values->device;
	$all = $common->capability;
	$v=objectToArray($all_values);
	$osVar=$v['device']['virtualcapability'][13];
	$osType=$osVar['@attributes']['value'];
				
				}
			// Check OS
		$result = OA_Dal_Delivery_query("SELECT os_name FROM oxm_operating_system WHERE os_id = '".$osArray[$i]."'");
		$row1 = mysql_fetch_row($result);
		
		
		if($row1[0]==$osType && $osType!="" && $osType!=" "){
		
		$redirectionFlag=1;
		}
		$optmise++;
			}
		}

	
	}
	
/*************** Redirection for OS Ends **************/




	
	
	

		



/************** Redirection Goes Here ************************/

if($redirectionFlag==1){

	// Getting the Website Redirect URL
		$resultWebsite = OA_Dal_Delivery_query("SELECT a.redirecturl FROM rv_affiliates a inner join rv_zones z on z.affiliateid=a.affiliateid WHERE z.zoneid = '".$_GET['zoneid']."'");
		$row2 = mysql_fetch_row($resultWebsite);
		// Set Timeout Function	for redirection	
		if($image=="no"){
		if($type=="no"){
			echo "<script>console.log('Redirecting for  Camapign ID: ".$campaignPK."');setTimeout(function(){ window.top.location.href='".$row2[0]."'; }, 10000);</script>";
			}
			else{
				echo MAX_javascriptToHTML("<script>console.log('Redirecting for  Camapign ID: ".$campaignPK."');setTimeout(function(){ window.top.location.href='".$row2[0]."'; }, 10000);</script>", 'OX_'.substr(md5(uniqid('', 1)), 0, 8));
				}
	}
	else{
// For Image Tag goes here
	}	
	
	
		}



	}

function objectToArray ($object) {
    if(!is_object($object) && !is_array($object))
        return $object;

    return array_map('objectToArray', (array) $object);
}
?>
