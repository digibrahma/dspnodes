<?php

/*
+---------------------------------------------------------------------------+
| OpenX  v2.8                                                              |
| ==========                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: advertiser-delete.php 49423 2010-02-26 20:19:26Z chris.nutting $
*/

// Require the initialisation file
require_once '../../../../init.php';

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

$recordId = $_GET['id'];

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

if (!empty($recordId)) {

    $ids = explode(',', $recordId);

    while (list(,$recordId) = each($ids)) {

	$query = mysql_query("delete from ".$table_prefix."carrier_detail where id=".$recordId) or die(mysql_error());

    }

    // Queue confirmation message
    $translation = new OX_Translation ();

    if (count($ids) == 1) {
        $translated_message = $translation->translate ("Selected Teleco Carrier Has Been deleted", array(
        ));
    } else {
        $translated_message = $translation->translate ("All Selected Teleco Carrier's Has Been deleted");
    }

    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
}


/*-------------------------------------------------------*/
/* Return to the index page                              */
/*-------------------------------------------------------*/

if (!isset($returnurl) && $returnurl == '') {
    $returnurl = 'carrier-detail.php';
}

header("Location: ".$returnurl);

?>
