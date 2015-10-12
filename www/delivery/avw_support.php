<?php
/*************     Customization for redirection     ****************/
require_once '../../init.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
require_once MAX_PATH . '/plugins/geoTargeting/oxMaxMindGeoIP/oxMaxMindGeoIP.delivery.php';

$zone=$_GET['zoneid'];

$geo = oxMaxMind_getGeo($_SERVER['REMOTE_ADDR'],MAX_PATH.'/plugins/geoTargeting/oxMaxMindGeoIP/data/GeoIP.dat');
        foreach ($geo as $feature => $value) {
            if (!empty($value) && empty($ret[$feature])) {
                $ret[$feature] = $geo[$feature];
            }
        }
define('country',$ret['country_code']);

$qry=OA_Dal_Delivery_query("select assoc.ad_id,ban.filename,ban.campaignid from rv_ad_zone_assoc  assoc inner join rv_banners ban on ban.bannerid=assoc.ad_id where assoc.zone_id='".$zone."'");
$campId;
$files;
$redirect;
$j=0;
$cookie_name="OAVARSCAMP".$zone;

echo "var campID='".$_COOKIE[$cookie_name]."';";
$position1 = strrpos($_COOKIE[$cookie_name], "#*");
$position1=$position1+2;
$position2 = strrpos($_COOKIE[$cookie_name], "!*");
$position2=$position2;

$cookieCamp=substr($_COOKIE[$cookie_name],$position1,$position2-$position1);

echo "var campID='".$cookieCamp."'; console.log(campID);";

while($row=OA_Dal_Delivery_fetchAssoc($qry)){
	
	if(pageRedirection($row['campaignid'])=="yes"){
		$resultWebsite = OA_Dal_Delivery_query("SELECT a.redirecturl FROM rv_affiliates a inner join rv_zones z on z.affiliateid=a.affiliateid WHERE z.zoneid = '".$_GET['zoneid']."'");
		$row2 = mysql_fetch_row($resultWebsite);
		if($row['campaignid']==$cookieCamp){
			echo "console.log('Redirecting for  Camapign ID: ".$row['campaignid']."');setTimeout(function(){ window.top.location.href='".$row2[0]."'; }, 10000);";
			}
	}
		else{
	}
	
		
	$j++;	
	}

function pageRedirection($campId,$type="yes",$image="no"){

$redirectionFlag=0; // Flag to Redirect

$campaignPK=$campId;


/*************** Redirection for Country Starts **************/



// Selecting Country Primary Key as Comma Seperated String
$qry=OA_Dal_Delivery_query("select country_fk from rv_campaign_redirect where campaign_fk='".$campaignPK."'");
while($row=OA_Dal_Delivery_fetchAssoc($qry)){
$countryArray = explode(',', $row['country_fk']);

for($i=0;$i<count($countryArray);$i++){
		$result = OA_Dal_Delivery_query("SELECT country_code FROM oxm_country WHERE id = '".$countryArray[$i]."'");
		$row1 = mysql_fetch_row($result);
		if($row1[0]==country && country!="" && country!=" "){
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

	return "yes";
	}
	else{
		return "no";
		}



	}

function objectToArray ($object) {
    if(!is_object($object) && !is_array($object))
        return $object;

    return array_map('objectToArray', (array) $object);
}
?>
