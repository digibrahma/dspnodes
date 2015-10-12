<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

// Require the initialisation file
require_once '../../init.php';
// Required files
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once LIB_PATH . '/Plugin/Component.php';

// Register input variables
phpAds_registerGlobal (
     'action'
    ,'trackerids'
    ,'clickwindowday'
    ,'clickwindowhour'
    ,'clickwindowminute'
    ,'clickwindows'
    ,'clickwindowsecond'
    ,'hideinactive'
    ,'statusids'
    ,'submit'
    ,'viewwindowday'
    ,'viewwindowhour'
    ,'viewwindowminute'
    ,'viewwindows'
    ,'viewwindowsecond'
);


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);
OA_Permission::enforceAccessToObject('clients',   $clientid);
OA_Permission::enforceAccessToObject('campaigns', $campaignid);

/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid'] = $clientid;
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid] = $campaignid;
phpAds_SessionDataStore();

$requestCampId="";
if(isset($_GET['campaignid'])){
	$requestCampId=$_GET['campaignid'];
	}
if(isset($_POST['campaignid'])){
	$requestCampId=$_POST['campaignid'];
	}

// Initalise any tracker based plugins
$plugins = array();
$invocationPlugins = &OX_Component::getComponents('invocationTags');
foreach($invocationPlugins as $pluginKey => $plugin) {
    if (!empty($plugin->trackerEvent)) {
        $plugins[] = $plugin;
        $fieldName = strtolower($plugin->trackerEvent);
        phpAds_registerGlobal("{$fieldName}windowday", "{$fieldName}windowhour", "{$fieldName}windowminute", "{$fieldName}windowsecond", "{$fieldName}windows");
    }
}

/*-------------------------------------------------------*/
/* Process submitted form                                */
/*-------------------------------------------------------*/

 $con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']) or die("not connect")or die(mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
 mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con) or die(mysql_error());

// One Time Entry Checking
$oneTimeQry=OA_Dal_Delivery_query("select ifnull(count(campaign_fk),0) as total from rv_campaign_redirect where campaign_fk='".$requestCampId."'");
$tableEntryStatus=0;
$oneTimeRow=mysql_fetch_row($oneTimeQry);
$tableEntryStatus=$oneTimeRow[0]; //Campaign Entry Status
// If Campaign is not stored in table(rv_campaign_redirect) means condition will get pass
if($tableEntryStatus==0){
OA_Dal_Delivery_query("insert into rv_campaign_redirect(campaign_fk) values('".$requestCampId."')");
}
 
// Processing the Form Submission
if (isset($_POST['countryValues']) || isset($_POST['osValues']) || isset($_POST['handsetValues']) || isset($_POST['action'])) {
$countryVal=" ";	
if(isset($_POST['countryValues'])){
$countryVal=implode (",", $_POST['countryValues']);
 }

$osVal=" ";
if(isset($_POST['osValues'])){
$osVal=implode (",", $_POST['osValues']);
 }

$hansetVal=" ";
if(isset($_POST['handsetValues'])){
 $hansetVal=implode (",", $_POST['handsetValues']);
 }


OA_Dal_Delivery_query("update  rv_campaign_redirect set  country_fk='".$countryVal."',operating_system='".$osVal."',handset='".$hansetVal."' where campaign_fk='".$requestCampId."' ");

}
//die($tableEntryStatus);


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/



// Initialise some parameters
$pageName = basename($_SERVER['SCRIPT_NAME']);
$tabindex = 1;
$agencyId = OA_Permission::getAgencyId();
$aEntities = array('clientid' => $clientid, 'campaignid' => $campaignid);

// Display navigation
$aOtherAdvertisers = Admin_DA::getAdvertisers(array('agency_id' => $agencyId));
$aOtherCampaigns = Admin_DA::getPlacements(array('advertiser_id' => $clientid));
MAX_displayNavigationCampaign($campaignid, $aOtherAdvertisers, $aOtherCampaigns, $aEntities);

if (!empty($campaignid)) {
    $doCampaigns = OA_Dal::factoryDO('campaigns');
    if ($doCampaigns->get($campaignid)) {
        $campaign = $doCampaigns->toArray();
    }
}

$tabindex = 1;


echo "<form action='campaign-redirect.php' method='post'>";
echo "\t\t\t\t<input type='hidden' name='campaignid' value='".$GLOBALS['campaignid']."'>\n";
echo "\t\t\t\t<input type='hidden' name='clientid' value='".$GLOBALS['clientid']."'>\n";
echo "\t\t\t\t<input type='hidden' name='action' value='set'>\n";		
		echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>"."\n";
echo "<tr><td height='25' width='100%' colspan='4'><b>Countries</b></td></tr>"."\n";
echo "<tr height='1'><td colspan='4' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>"."\n";
echo "<tr><td height='10' colspan='4'>&nbsp;</td></tr>"."\n";


// Header
echo "\t\t\t\t\n";

/***** 		Country Starts Here  *********/

echo "<tr><td height='25' width='100%' colspan='4'><div class='box'>";
$innerQry=OA_Dal_Delivery_query("select country_fk from rv_campaign_redirect where campaign_fk='".$requestCampId."'");
	$innerResult=mysql_fetch_row($innerQry);
	$temp=explode (",", $innerResult[0]);

$qry=OA_Dal_Delivery_query("select * from oxm_country");
while($row=OA_Dal_Delivery_fetchAssoc($qry)){
	
	$control=0;
	for($k=0;$k<count($temp);$k++){
	if($temp[$k]==$row['id']){
	$control=1;
		}	
		}
	
	if($control==1){
		echo "<div><input type='checkbox'  name='countryValues[]' value='".$row['id']."' checked>".$row['name']."</input></div>";
		}
		else{
		echo "<div><input type='checkbox'  name='countryValues[]' value='".$row['id']."' >".$row['name']."</input></div>";	
			}	
	
	
}	
	
	

echo "</div></td></tr>";
		
		
		echo "</table></br>";
		
		echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>"."\n";
		echo "<tr><td height='25' width='100%' colspan='4'><b>Operating System</b></td></tr>"."\n";
		echo "<tr height='1'><td colspan='4' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>"."\n";
		echo "<tr><td height='10' colspan='4'>&nbsp;</td></tr>"."\n";
		

		
		/***** 		OS Starts Here  *********/
		
		echo "<tr><td height='25' width='100%' colspan='4'><div class='box'>";
		
		$innerQry=OA_Dal_Delivery_query("select operating_system from rv_campaign_redirect where campaign_fk='".$requestCampId."'");
	$innerResult=mysql_fetch_row($innerQry);
	$temp=explode (",", $innerResult[0]);
	
		$qry=OA_Dal_Delivery_query("select * from oxm_operating_system");
		while($row=OA_Dal_Delivery_fetchAssoc($qry)){
		
		
		$control=0;
	for($k=0;$k<count($temp);$k++){
		
	if($temp[$k]==$row['os_id']){
	
	$control=1;
		}	
		}
	
	if($control==1){
		echo "<div><input type='checkbox'  name='osValues[]' value='".$row['os_id']."' checked>".$row['os_name']."</input></div>";
	
		}
		else{
			echo "<div><input type='checkbox'  name='osValues[]' value='".$row['os_id']."' >".$row['os_name']."</input></div>";
			}	
		
		}
		
		
		
		echo "</div></td></tr>";
		
		
		echo "</table></br>";
		

/***** 		OS Ends Here  *********/



		echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>"."\n";
		echo "<tr><td height='25' width='100%' colspan='4'><b>Handset</b></td></tr>"."\n";
		echo "<tr height='1'><td colspan='4' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>"."\n";
		echo "<tr><td height='10' colspan='4'>&nbsp;</td></tr>"."\n";
		
		
		
		
		/***** 		Handset Starts Here  *********/
		
		echo "<tr><td height='25' width='100%' colspan='4'><div class='box'>";
	
	$innerQry=OA_Dal_Delivery_query("select handset from rv_campaign_redirect where campaign_fk='".$requestCampId."'");
	$innerResult=mysql_fetch_row($innerQry);
	$temp=explode (",", $innerResult[0]);
		
		$qry=OA_Dal_Delivery_query("select * from oxm_teleco");
		while($row=OA_Dal_Delivery_fetchAssoc($qry)){
				
		$control=0;
	for($k=0;$k<count($temp);$k++){
		
	if($temp[$k]==$row['id']){
	
	$control=1;
		}	
		}
	
	if($control==1){
			echo "<div><input type='checkbox'  name='handsetValues[]' value='".$row['id']."' checked>".$row['name']."</input></div>";
	
		}
		else{
				echo "<div><input type='checkbox'  name='handsetValues[]' value='".$row['id']."' >".$row['name']."</input></div>";
				}	
	
		}
		
		
		
		echo "</div></td></tr>";
		
		
		echo "<tr><td height='50' width='100%' colspan='4'><input type='Submit' value='Save Changes'/></td></tr>
		</table></form></br></br>";
		
		/***** 		Handset Ends Here  *********/
		



?>
<script>
$(document).ready(function(){
	$('#thirdLevelTools').children().each(function(){

$(this).remove();
		});;
	});
</script>

<?php
/*-------------------------------------------------------*/
/* Store preferences                                     */
/*-------------------------------------------------------*/

$session['prefs']['campaign-trackers.php']['listorder'] = $listorder;
$session['prefs']['campaign-trackers.php']['orderdirection'] = $orderdirection;

phpAds_SessionDataStore();


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>
