<?php
//error_log('Test',3,"error.log");
require_once '../../init.php';

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

	if(!$con)  
	{
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		//error_log('Test',3,"error.log");
		$auctionId 	= 	$_REQUEST['auctionId'];
		$bidid 		= 	$_REQUEST['bidid'];
		$price 		= 	$_REQUEST['price'];
		$impid 		= 	$_REQUEST['impid'];
		$seatid 	= 	$_REQUEST['seatid'];
		$adid 		= 	$_REQUEST['adid'];
		$cur 		= 	$_REQUEST['cur'];		
		$cur_date 	= 	date('Y-m-d H:i:s');
//error_log("select id from rv_dj_axonix_bid_request where bid_request_id='$bidid'", 3, 'error.log');
		$fetchdata=mysql_fetch_array(mysql_query("select id from rv_dj_axonix_bid_request where bid_request_id='$bidid'"));



		/********** Insert Win notice into AFF_SMAATO_WIN_NOTICE **********/
		mysql_query("INSERT INTO  
						`rv_dj_axonix_win_notice` (
							`datetime`,
							`auctionID`,
							 request_id,
							`bidid` ,
							`price` ,
							`currency` ,
							`impid` ,
							`seatid` ,
							`adid`
						)
						VALUES (
							 '".$cur_date."',
							 '".$auctionId."',
							 '".$fetchdata['id']."',
							 '".$bidid."',
							 '".$price."',
							 '".$cur."',
							 '".$impid."',
							 '".$seatid."',
							 '".$adid."'
							 
						)");

		
	}

