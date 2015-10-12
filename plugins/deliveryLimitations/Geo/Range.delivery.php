<?php


/**
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Chris Nutting <chris@m3.net>
 * @author     Andrzej Swedrzynski <andrzej.swedrzynski@m3.net>
 */

require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';
require_once MAX_PATH . '/lib/max/other/lib-geo.inc.php';

require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

/**
 * Check to see if this impression contains the valid latitude/longitude.
 *
 * @param string $limitation The latitude/longitude limitation
 * @param string $op The operator (either '==' or '!=')
 * @param array $aParams An array of additional parameters to be checked
 * @return boolean Whether this impression's latitude/longitude passes this limitation's test.
 */
// Ip Range Checking
function MAX_checkGeo_Range($limitation, $op, $aParams = array())
{
	print_r(($limitation);	
	$flag="0";
	
	$qry=OA_Dal_Delivery_query("select hostmin as minimum,hostmax as maximum from djax_iprange where 
			locid='".$limitation."'");
	while($row=mysql_fetch_array($qry)){
		
	$start=sprintf('%u', ip2long($row["minimum"]));;
	$stop=sprintf('%u', ip2long($row["maximum"]));
	$check=sprintf('%u', ip2long($_SERVER['REMOTE_ADDR']));;



if($start<=$check && $stop>=$check){
	$flag="1";
	
}
}


	if($flag=="1"){
		return true;
		
	}
	else{
		return false;
	}
	
	
   
}
	

?>
