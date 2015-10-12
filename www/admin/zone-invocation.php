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
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/max/Admin/Invocation.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccessToObject('affiliates', $affiliateid);
OA_Permission::enforceAccessToObject('zones', $zoneid);

if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
    OA_Permission::enforceAllowed(OA_PERM_ZONE_INVOCATION);
}

/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid'] = $affiliateid;
phpAds_SessionDataStore();

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
$mobilezonetype=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select app_select from {$table_prefix}zones  where zoneid='$zoneid'"));
$select=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select * from {$table_prefix}dj_admin_configuration"));
	
if($mobilezonetype['app_select']==2)
{
	if(!empty($select['SDK_Androidpath']))
	{
	header("Location:".$select['SDK_Androidpath']);
	}
	else	
	{
		$url="http://".$GLOBALS['_MAX']['CONF']['webpath']['admin']."account-settings-adminconfiguration.php";
		echo "<b><i>Please configure the App path in Admin($url)</i></b>";
	}
}
else if($mobilezonetype['app_select']==1)
{
	if(!empty($select['SDK_iOSpath']))
	{
	header("Location:".$select['SDK_iOSpath']);
	}
	else
	{
		$url="http://".$GLOBALS['_MAX']['CONF']['webpath']['admin']."account-settings-adminconfiguration.php";
		echo "<b><i>Please configure the App path in Admin($url)</i></b>";
	}
}

else
{


// Initialise some parameters
$pageName = basename($_SERVER['SCRIPT_NAME']);
$tabIndex = 1;
$agencyId = OA_Permission::getAgencyId();
$aEntities = array('affiliateid' => $affiliateid, 'zoneid' => $zoneid);

$aOtherPublishers = Admin_DA::getPublishers(array('agency_id' => $agencyId));
$aOtherZones = Admin_DA::getZones(array('publisher_id' => $affiliateid));
MAX_displayNavigationZone($pageName, $aOtherPublishers, $aOtherZones, $aEntities);

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

$dalZones = OA_Dal::factoryDAL('zones');
if ($zone = $dalZones->getZoneForInvocationForm($zoneid)) {
    $extra = array('affiliateid' => $affiliateid,
                   'zoneid' => $zoneid,
                   'width' => $zone['width'],
                   'height' => $zone['height'],
                   'delivery' => $zone['delivery'],
                   'website' => $zone['website']
    );
    // Ensure 3rd Party Click Tracking defaults to the preference for this agency
    if (!isset($thirdpartytrack)) {
        $thirdpartytrack = $GLOBALS['_MAX']['CONF']['delivery']['clicktracking'];
    }
    $maxInvocation = new MAX_Admin_Invocation();
    echo $maxInvocation->placeInvocationForm($extra, true);
}

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();
}

?>
