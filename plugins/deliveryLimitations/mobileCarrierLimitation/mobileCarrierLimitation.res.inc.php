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
$Id: Os.res.inc.php 49216 2010-02-19 16:26:59Z matthieu.aubry $
*/

/**
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Chris Nutting <chris.nutting@openx.org>
 */
require_once '../../init.php';


$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$sel = mysql_query("select country,carriername from ".$table_prefix."carrier_detail ORDER BY country");
$i=0;
while($sele = mysql_fetch_array($sel)){


$res1[$sele['country']] = $sele['country'];
//$res1[$sele['country']][++$i]= $sele['carriername'];
}

$res = $res1;


?>
