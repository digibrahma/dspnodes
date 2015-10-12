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
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

// Register input variables
phpAds_registerGlobal ('returnurl');

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

$profileid = $_GET['profileid'];

if (!empty($profileid)) {

    $ids = explode(',', $profileid);

    while (list(,$profileid) = each($ids)) {

	$query = mysql_query("delete from oxm_profile where profileid=".$profileid) or die(mysql_error());

    }

    // Queue confirmation message
    $translation = new OX_Translation ();

    if (count($ids) == 1) {
        $translated_message = $translation->translate ("Selected Profile Has Been deleted", array(
        ));
    } else {
        $translated_message = $translation->translate ("All Selected Profile Has Been deleted");
    }

    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
}


/*-------------------------------------------------------*/
/* Return to the index page                              */
/*-------------------------------------------------------*/

if (!isset($returnurl) && $returnurl == '') {
    $returnurl = 'account-settings-custom-profile.php';
}

header("Location: ".$returnurl);

?>
