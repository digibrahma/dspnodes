<?php

/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                                                |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                                                                |
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
$Id: demoLimitation.delivery.php 33995 2009-03-18 23:04:15Z chris.nutting $
*/

/**
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Chris Nutting <chris@m3.net>
 * @author     Andrzej Swedrzynski <andrzej.swedrzynski@m3.net>
 */

require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

/**
 * Check to see if this impression contains the valid IP Address.
 *
 * @param string $limitation The IP address limitation
 * @param string $op The operator (either '==' or '!=')
 * @param array $aParams An array of additional parameters to be checked
 * @return boolean Whether this impression's IP address passes this limitation's test.
 */
function MAX_checkMobileCarrierLimitation_MobileCarrierLimitation($limitation, $op, $aParams = array())
{
//echo $limitation;

		$ip = $_SERVER['REMOTE_ADDR'];
		

			$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
			$GLOBALS['_MAX']['CONF']['database']['password']);
			mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
			$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

			$del = mysql_query("SELECT * FROM ".$table_prefix."carrier_detail WHERE INET_ATON(start_ip) <= INET_ATON('".$ip."') AND INET_ATON(end_ip) >= INET_ATON('".$ip."')")or die('Not ');


			while($deli = mysql_fetch_array($del)){

			$delivery[] = $deli['country'];
			$delivery[] = $deli['id'];

			}

 $res = explode('|',$limitation);

 
return MAX_limitationsMatchStringValue($delivery[0], $res[0], $op) && 
 	MAX_limitationsMatchArrayValue($delivery[1], $res[1], $op);


}
?>
