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
$oneTimeQry=OA_Dal_Delivery_query("select ifnull(count(campaignid),0) as total from rv_campaign_iprange where campaignid='".$requestCampId."'");
$tableEntryStatus=0;
$oneTimeRow=mysql_fetch_row($oneTimeQry);
$tableEntryStatus=$oneTimeRow[0]; //Campaign Entry Status
// If Campaign is not stored in table(rv_campaign_iprange) means condition will get pass
if($tableEntryStatus==0){
OA_Dal_Delivery_query("insert into rv_campaign_iprange(campaignid) values('".$requestCampId."')");
}
 
// Processing the Form Submission
if (isset($_POST['iprange']) || isset($_POST['action'])) {
$ipVal=" ";	
if(isset($_POST['iprange'])){
$ipVal=implode (",", $_POST['iprange']);
 }




OA_Dal_Delivery_query("update  rv_campaign_iprange set  ipranges='".$ipVal."' where campaignid='".$requestCampId."' ");

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


echo "
<style>.innerDiv{height:20px;} .lblcolor{ color:#ff217e;}
.fsip{width:90%; margin:7px; border:1px solid #000; }
.fsip legend{ font-size:12px; padding-left:3px; color:#0767a8; padding-right:3px; }
.selectall:hover,.deselectall:hover{
cursor:pointer;
}
</style>
<form action='campaign-iprange.php' method='post'>";
echo "\t\t\t\t<input type='hidden' name='campaignid' value='".$GLOBALS['campaignid']."'>\n";
echo "\t\t\t\t<input type='hidden' name='clientid' value='".$GLOBALS['clientid']."'>\n";
echo "\t\t\t\t<input type='hidden' name='action' value='set'>\n";		
		echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>"."\n";
echo "<tr><td height='25' width='100%' colspan='4'><b>Select IP Range's</b></td></tr>"."\n";
echo "<tr height='1'><td colspan='4' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>"."\n";
echo "<tr><td height='10' colspan='4'>&nbsp;</td></tr>"."\n";


// Header
echo "\t\t\t\t\n";

/***** 		Never Change the UI Structure, it affects the checkbox  *********/

echo "<tr><td  width='100%' colspan='4'><div class='box' style='height:360px; width:100%;'>";
$innerQry=OA_Dal_Delivery_query("select ipranges from rv_campaign_iprange where campaignid='".$requestCampId."'");
	$innerResult=mysql_fetch_row($innerQry);
	$temp=explode (",", $innerResult[0]);
$headerRelation="nil";
$qry=OA_Dal_Delivery_query("select * from djax_iprange");
$firstEntry=0;
while($row=OA_Dal_Delivery_fetchAssoc($qry)){
	if($firstEntry==0){
		echo "<fieldset class='fsip'><legend><b>".$row['name']."</b></legend><div class='innerDiv'><a class='selectall' onclick='customFunction(this);'>Check All</a>&nbsp;/&nbsp;<a class='deselectall' onclick='customFunction(this);'>Uncheck All</a></div>";
		$headerRelation=$row['name'];
		}
		$firstEntry++;
	$control=0;
	for($k=0;$k<count($temp);$k++){
	if($temp[$k]==$row['locid']){
	$control=1;
		}	
		}
	if($headerRelation!=$row['name']){
		echo "</fieldset><fieldset class='fsip'><legend><b>".$row['name']."</b></legend><div class='innerDiv'><a class='selectall' onclick='customFunction(this);'>Check All</a>&nbsp;/&nbsp;<a class='deselectall' onclick='customFunction(this);'>Uncheck All</a></div>";
		$headerRelation=$row['name'];
		}
	if($control==1){
		
		echo "<div class='innerDiv'><input class='innerCheck' type='checkbox'  name='iprange[]' value='".$row['locid']."' checked>".$row['name']." - <b>".$row['ipaddress']."&nbsp;&nbsp;<label  class='lblcolor'>[".$row['hostmin']."&nbsp;-&nbsp;".$row['hostmax']."]</label>"."</b></input></div>";
		}
		else{
		echo "<div class='innerDiv'><input type='checkbox' class='innerCheck'   name='iprange[]' value='".$row['locid']."' >".$row['name']." - <b>".$row['ipaddress']."&nbsp;&nbsp;<label class='lblcolor'>[".$row['hostmin']."&nbsp;-&nbsp;".$row['hostmax']."]</label>"."</b></input></div>";	
			}	
	
	
}	
	
echo "</fieldset>";	

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

function customFunction(obj){
	
	$(obj).parent().parent().children('div').each(function(){
		$(this).children().each(function(){
			
			if($(this).attr('type')=='checkbox'){
				if($(obj).attr('class')=='selectall'){
					$(this).attr('checked', true);
					}
					else{
						$(this).attr('checked', false);
						}
				
				}
			
			});
		
		});
	}
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
