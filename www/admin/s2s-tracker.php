<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
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
$Id: affiliate-channels.php 81772 2012-09-11 00:07:29Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once LIB_PATH . '/Plugin/Component.php';


$oPluginManager = new OX_PluginManager();

$oComponentGroupManager = new OX_Plugin_ComponentGroupManager();

phpAds_PageHeader('s2s-tracker', $oHeaderModel);

$conf = $GLOBALS['_MAX']['CONF'];

$deliverypath=$GLOBALS['_MAX']['SSL_REQUEST'] ? 'https://' . $conf['webpath']['deliverySSL'] : 'http://' . $conf['webpath']['delivery'];


$track_url="<div id='tracker' style='position: absolute; left: 0px; top: 0px; visibility: hidden;'><img src='$deliverypath/tracker-mobile.php?transaction_id={transaction_id}' width='0' height='0' alt='' /></div>";


echo "<form action='' method='post'><table width='100%' border='0' cellspacing='0' cellpadding='0'>";

echo "<tr width='100%'><td width='100%'><b><h3>Advertiser Post Back Tracker Pixel</h3></b></td></tr>";

echo "<tr width='100%'>";

echo "<td width='100%'>

<textarea class='code-gray' id='bannercode' name='bannercode'  type='text' name='tracker_url' id='tracker_url'  style='width: 95%; border: 1px solid black;' rows='3' cols='70' onclick='this.select();'>$track_url</textarea></td></tr>";

//echo "<tr width='100%'><td width='10%'></td><td width='50%'><input type='submit' name='update' name='save' id='save'> </td></tr>";

echo "</table></form>";


phpAds_PageFooter();

?>
