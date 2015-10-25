<?php





$bannerid = $_REQUEST['bannerid'];
$zoneid = $_REQUEST['zoneid'];
$pid = $_REQUEST['af_cid'];
$pid2 = $_REQUEST['af_tid'];

switch($bannerid) {

case '201':
	$bannerid='325';
	$zoneid='55';
break;

case '202':
	$bannerid='326';
	$zoneid='55';
	
break;

case '203':
	$bannerid='324';
	$zoneid='55';
	
break;

case '183':
	$bannerid='328';
	$zoneid='55';
	
break;

case '184':
	$bannerid='328';
	$zoneid='55';
	
break;

case '182':
	$bannerid='328';
	$zoneid='55';
	
break;

case '179':
	$bannerid='323';
	$zoneid='55';
	
break;




}


$NewURL = "http://rig.digibrahma.in/www/delivery/ck.php?oaparams=2__bannerid=".$bannerid."__zoneid=".$zoneid."__pid=".$pid."__pid2=".$pid2."__cb=09dfa5f74a__transaction_id={transaction_id}";
header("Location: $NewURL;");exit;
?>