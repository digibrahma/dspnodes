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
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
//require_once MAX_PATH . '/lib/OA/Central/AdNetworks.php';

// Register input variables
phpAds_registerGlobal ('returnurl');


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccessToObject('affiliates', $affiliateid);

if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
    OA_Permission::enforceAllowed(OA_PERM_ZONE_DELETE);
}

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

if (!empty($zoneid)) {
    $ids = explode(',', $zoneid);
    while (list(,$zoneid) = each($ids)) {

        // Security check
        OA_Permission::enforceAccessToObject('zones', $zoneid);
    
        $doZones = OA_Dal::factoryDO('zones');
        $doZones->zoneid = $zoneid;
        if ($doZones->get($zoneid)) {
            $aZone = $doZones->toArray();
        }

        // Ad  Networks
        //$oAdNetworks = new OA_Central_AdNetworks();
        //$oAdNetworks->deleteZone($doZones->as_zone_id);
	mysql_query("delete from oxm_mobilezones where masterzoneid =".$zoneid) or die(mysql_error());

	mysql_query("delete from rv_zones where masterzone =".$zoneid) or die(mysql_error());

        $doZones->delete();
    }
    
    // Queue confirmation message
    $translation = new OX_Translation ();
    
    if (count($ids) == 1) {
        $translated_message = $translation->translate ($GLOBALS['strZoneHasBeenDeleted'], array(
            htmlspecialchars($aZone['zonename'])
        ));
    } else {
        $translated_message = $translation->translate ($GLOBALS['strZonesHaveBeenDeleted']);
    }

    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
}

if (!isset($returnurl) && $returnurl == '') {
    $returnurl = 'affiliate-zones.php';
}

Header("Location: ".$returnurl."?affiliateid=$affiliateid");

?>
