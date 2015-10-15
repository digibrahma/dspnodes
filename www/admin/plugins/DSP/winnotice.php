<?php

require_once '../../../../init.php';

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

	
	if(!$con) 
	{
		die('Could not connect: ' . mysql_error());
	}
	else
	{
	//	error_log(print_r($_REQUEST, TRUE), 3, 'notice.log');
		$auctionId 	= 	$_REQUEST['auctionId'];
		$bidid 		= 	$_REQUEST['bidid'];
		$price 		= 	$_REQUEST['price'];
		$impid 		= 	$_REQUEST['impid'];
		$seatid 	= 	$_REQUEST['seatid'];
		$adid 		= 	$_REQUEST['adid'];
		$cur 		= 	$_REQUEST['cur'];		
		$cur_date 	= 	date('Y-m-d H:i:s');

		//$fetchdata=mysql_fetch_assoc(mysql_query("select id from {$table_prefix}dj_dsp_bid_request where bid_request_id='$bidid'"));
		
		/********** Insert Win notice into AFF_SMAATO_WIN_NOTICE **********/
		$sql = "INSERT INTO  
						`{$table_prefix}dj_dsp_win_notice` (
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
							 
						)";
				
				
					
			//mysql_query("update {$table_prefix}dj_dsp_response set win_notice='1' where id='".$bidid."'");	
				
		
		if(mysql_query($sql))
		{
			mysql_close();
			//mysql_query("INSERT INTO rv_imp_ck(`ad_id`,`date_time`)VALUES ('".$adid."','".$cur_date."')");
		// if(mysql_query("update {$table_prefix}dj_dsp_response set win_notice='1' where id='".$bidid."'"))
		 {
			 //error_log("update {$table_prefix}dj_dsp_response set win_notice='1' where id='".$bidid."'",3,"error.log");
			//@mail('nduraisamy3@gmail.com','samtto test','Live check=>'.$bidid);
		 }
		 
		} 
		//error_log($sql,3,"error.log");
	//error_log("\n",3,"error.log");
	}
?>
