<?php 
require_once '../../../../init.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
require_once LIB_PATH . '/Plugin/PluginManager.php';
require_once LIB_PATH . '/Plugin/ComponentGroupManager.php';
phpAds_PageHeader('dsp-statistics');

OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN,OA_ACCOUNT_TRAFFICKER,OA_ACCOUNT_MANAGER,OA_ACCOUNT_ADVERTISER);


	$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
	mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
	$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
	

$_period = !empty($_POST['period_preset']) ? $_POST['period_preset'] : '';

if($_POST['period_preset']!='')
{
	
	if($_POST['period_preset']=='today')
	{		
		$start=date("Y-m-d 00:00:00");//$date=date("Y-m-d H:00:00");
	    $end=date("Y-m-d H:00:00");
	   
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
		
		$ex=date("Y-m-d 00:00:00");
		
	}
	else if($_POST['period_preset']=='yesterday')
	{		
		
		$start=date('Y-m-d', strtotime('Yesterday'));
		$end=date('Y-m-d', strtotime('Yesterday'));
		
		$ex=date('Y-m-d', strtotime('Yesterday'));
	
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
	

	}
	/*else if($_POST['period_preset']=='this_week')
	{
		$start=date('Y-m-d', strtotime('this week Monday'));
		$end=date('Y-m-d');
	}
	else if($_POST['period_preset']=='last_week')
	{
		$start=date('Y-m-d', strtotime('previous Monday'));
		$end=date('Y-m-d', strtotime('previous Sunday'));
	}*/
	else if($_POST['period_preset']=='last_7_days')
	{
		
		$start=date('Y-m-d', strtotime('Today - 7 Day'));
		$end=date('Y-m-d');
		
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
		
		$ex=date('Y-m-d', strtotime('Today - 7 Day'));
	}
	else if($_POST['period_preset']=='this_month')
	{
		
		
		$start=date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
		$end=date('Y-m-d');
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
		
		$ex=date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
	}
	else if($_POST['period_preset']=='last_month')
	{
		
		$start=date('Y-m-d', mktime(0, 0, 0, (date('m') - 1), 1, date('Y')));
		$end=date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y')));
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
		
		$ex=date('Y-m-d', mktime(0, 0, 0, (date('m') - 1), 1, date('Y')));
	}
	else if($_POST['period_preset']=='all_stats')
	{
	
		$start=date("0000-00-00 00:00:00");
		$end=date("Y-m-d H:00:00");
		
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
		
		$ex=date("0000-00-00 00:00:00");
	}
	else if($_POST['period_preset']=='specific')
	{
		
		
		$start=$_POST['period_start'];
		$end=$_POST['period_end'];
		
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
		
		$ex=$_POST['period_start'];
	}
		
}
else
{
	
	$start=date("Y-m-d 00:00:00");//$date=date("Y-m-d H:00:00");
	    $end=date("Y-m-d H:00:00");
	   
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
}

//////////timezone

function check_iso_date($date)
{

	    if(!preg_match('/^(\d\d\d\d)-(\d\d?)-(\d\d?)$/', $date, $matches))
	    { 
		return false;
	    }
	   return $matches[0];
	 
		
} 

function time_zone($start,$end)
{
$con1 = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con1)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

	check_iso_date($start);
	check_iso_date($end);

    if($start== check_iso_date($start) && $end == check_iso_date($end))
	{

		$timezone_query =mysql_query("select preference_id  from ".$table_prefix."preferences where preference_name='timezone'");
		$timezone_row = mysql_fetch_array($timezone_query);
		$timezone_id = $timezone_row['preference_id'];


		$query = mysql_query("select value from ".$table_prefix."account_preference_assoc where preference_id=".$timezone_id) ;
		$row = mysql_fetch_array($query);
		$timezone = $row['value'];
	
	
		$value_query = mysql_query("select value from oxm_timezone where timezone='".$timezone."' ") ;
		$value_row = mysql_fetch_array($value_query);
		$offset = $value_row['value'];

	
		$offsetarr = explode('.',$offset);
		$hour = $offsetarr['0'];
		$minute = $offsetarr['1'];
	
		// Start Time calculation
	
		if ($start =='')
			$start_time = '1990-01-01';
		else 
			$start_time = $start;

		$start = strtotime($start_time);

	
		if($hour<0)
			$start = $start - ($hour * 60 - $minute) * 60;  //-4 h 30 m = -270
		else
			$start = $start - ($hour * 60 + $minute) * 60;  //4 h 30 m +270
	
		$start = date("Y-m-d H:i:s",$start);


		// End Time Calculation
	
		if ($end == '')
			$end_time = date("Y-m-d");
		else 
			$end_time = $end;
		
		 $end = strtotime($end_time);
	
	
		if($hour<0)
			$end = $end + (24*60*60) - ($hour * 60 - $minute) * 60 -1;  //-4 h 30 m = -270
		else
			$end = $end + (24*60*60)- ($hour * 60 + $minute) * 60 -1;  //4 h 30 m +270
	
		$end= date("Y-m-d H:i:s",$end);
	
	}
	
	else
	{

		$start = $start;
		$end = $end;
	}

$date[]=$start;
$date[]=$end;

	return $date;
}

///////////timezone
	$adExchange='default';
	$win_notice_tbl	=	'rv_dj_dsp_win_notice';
	$request_tbl	=	'rv_dj_dsp_bid_request';
	$response_tbl	=	'rv_dj_dsp_response';
	if(isset($_POST['exchange_id']) && $_POST['exchange_id']=='axonix')
	{
		$adExchange='axonix';
		$win_notice_tbl	=	'rv_dj_axonix_win_notice';
		$request_tbl	=	'rv_dj_axonix_bid_request';
		$response_tbl	=	'rv_dj_axonix_response';
	}
 $agency_id = OA_Permission::getEntityId();
 
 	if($clientid)
	{ //echo 'Test 1'; exit;
			$conf 		= 	$GLOBALS['_MAX']['CONF']['database'];
			$host		=	$conf['host'];
			$db			=	$conf['name'];	
			$dbh 		= 	new PDO("mysql:host=$host;dbname=$db", $conf['username'],  $conf['password']) or die(mysql_error());
			$sql 		= 	"call getRepResClient('".$startdate."','".$endate."','".$clientid."','".$adExchange."')";
			$statment	=	$dbh->query($sql);
			$reqcounts 	=	$statment->fetchAll(PDO::FETCH_ASSOC);
			$dbh 		= 	null;	
		/*$reqcount	=	OA_Dal_Delivery_query("	SELECT count(a.id) as req,count(b.response_id) as res,a.exchange_id 
												FROM rv_dj_dsp_bid_request a 
												left join `rv_dj_dsp_response` b on (b.requset_id= a.id and b.datetime=a.datetime)
												left JOIN rv_banners as oxb ON oxb.bannerid=b.adid 
												left JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid 
												left JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=a.exchange_id
												where DATE(a.datetime) between '$startdate' and '$endate' and oxcl.clientid=$clientid group by a.exchange_id");*/
													 
		$stat	=	OA_Dal_Delivery_query("SELECT req.exchange_id as exchange_id,IFNULL(COUNT(win.id),0) as win_count,
										IFNULL(SUM(win.price),0) as win_price, IFNULL(SUM(res.advertiser_bid_price),0)
										as bid_price FROM $win_notice_tbl as win 
										left join $response_tbl as res on res.requset_id= win.request_id 
										left join $request_tbl req on req.id=res.requset_id 
										LEFt JOIN rv_banners as oxb ON oxb.bannerid=res.adid 
										LEFT JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
										LEFt JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
										LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 
										WHERE 1 AND DATE(res.datetime) between  '$startdate' and '$endate' and oxcl.clientid=$clientid   AND DATE(win.datetime) between '$startdate' and '$endate'AND DATE(res.datetime) between '$startdate' and '$endate' 
										GROUP BY DATE(req.datetime),req.exchange_id
										ORDER BY DATE(req.datetime) DESC");
	}
	else
	{	 
		// admin Stat
		if($agency_id==0)
		{
			$conf 		= 	$GLOBALS['_MAX']['CONF']['database'];
			$host		=	$conf['host'];
			$db			=	$conf['name'];	
			$dbh 		= 	new PDO("mysql:host=$host;dbname=$db", $conf['username'],  $conf['password']) or die(mysql_error());
			$sql 		= 	"call getRepResAdmin('".$startdate."','".$endate."','".$adExchange."')";
			$statment	=	$dbh->query($sql);
			$reqcounts 	=	$statment->fetchAll(PDO::FETCH_ASSOC);
			$dbh 		= 	null;
			

			/*$reqcount	=	OA_Dal_Delivery_query("	SELECT count(a.id) as req,count(b.response_id) as res,a.exchange_id 
													FROM rv_dj_dsp_bid_request a 
													left join `rv_dj_dsp_response` b on (b.requset_id= a.id and b.datetime=a.datetime)
													left JOIN rv_banners as oxb ON oxb.bannerid=b.adid 
													left JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid 
													left JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
													LEFT JOIN rv_dj_dsp as adx ON adx.id=a.exchange_id
													 where DATE(a.datetime) between '$startdate' and '$endate'  group by a.exchange_id");*/				
												
			$stat	=	OA_Dal_Delivery_query("	SELECT req.exchange_id as exchange_id,IFNULL(COUNT(win.id),0) as win_count,
												IFNULL(SUM(win.price),0) as win_price,IFNULL(SUM(res.advertiser_bid_price),0) as bid_price
												FROM $win_notice_tbl as win 
												left join $response_tbl as res on res.requset_id= win.request_id 
												left join $request_tbl req on req.id=res.requset_id 
												LEFt JOIN rv_banners as oxb ON oxb.bannerid=res.adid 
												LEFT JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
												LEFt JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 
												WHERE 1 AND DATE(res.datetime) between  '$startdate' and '$endate' AND DATE(win.datetime) between '$startdate' and '$endate'
												AND DATE(res.datetime) between '$startdate' and '$endate' 
												GROUP BY DATE(req.datetime),req.exchange_id
												ORDER BY DATE(req.datetime) DESC");
		
		}
		
		else
		{
	
			//echo 'Test 3'; exit;
			//echo $startdate.'<>'.$endate."<>".$adExchange; exit; 
			
			$conf 		= 	$GLOBALS['_MAX']['CONF']['database'];
			$host		=	$conf['host'];
			$db			=	$conf['name'];	
			$dbh 		= 	new PDO("mysql:host=$host;dbname=$db", $conf['username'],  $conf['password']) or die(mysql_error());
			//$sql 		= 	"call getRepResByManager('".$startdate."','".$endate."','6','".$agency_id."')";
			$sql 		= 	"call getRepResAdmin('".$startdate."','".$endate."','".$adExchange."')";
			$statment	=	$dbh->query($sql);
			$reqcounts 	=	$statment->fetchAll(PDO::FETCH_ASSOC);
			//echo '<pre>';
			//print_r($reqcounts); exit;
			//echo "test";
			
			$dbh 		= 	null;
			
			/*$reqcount	=	OA_Dal_Delivery_query("	SELECT count(a.id) as req,count(b.response_id) as res,a.exchange_id 
													FROM rv_dj_dsp_bid_request a 
													left join `rv_dj_dsp_response` b on (b.requset_id= a.id and b.datetime=a.datetime)
													left JOIN rv_banners as oxb ON oxb.bannerid=b.adid 
													left JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid 
													left JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
													LEFT JOIN rv_dj_dsp as adx ON adx.id=a.exchange_id
													 where DATE(a.datetime) between '$startdate' and '$endate'  group by a.exchange_id");*/
			$existing_query="	SELECT req.exchange_id as exchange_id,
												 IFNULL(COUNT(win.id),0) as win_count,
												 IFNULL(SUM(win.price),0) as win_price,
												 IFNULL(SUM(res.advertiser_bid_price),0) as bid_price
												FROM $win_notice_tbl as win 
												left join $response_tbl as res on res.requset_id= win.request_id 
												left join $request_tbl	 req on req.bid_request_id=res.id 
												LEFt JOIN rv_banners as oxb ON oxb.bannerid=res.adid 
												LEFT JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
												LEFt JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 		
												WHERE 1 AND DATE(res.datetime) between  '$startdate' and '$endate' 
												and oxcl.agencyid='$agency_id' 
												
												GROUP BY DATE(req.datetime),req.exchange_id
												ORDER BY DATE(req.datetime) DESC";	
				
			$stat	=	OA_Dal_Delivery_query("SELECT count(win.id) as win_count ,sum(win.price) as win_price,sum(res.advertiser_bid_price) as bid_price FROM $win_notice_tbl win join $response_tbl res on res.id=win.bidid where date(win.datetime)='$startdate'");
							
		}
			
	}
	
	
	


	

?>


<form method='post'>
	<?php
		$adExchanges=mysql_query("select * from rv_dj_dsp where status = 1");
	?>
		<select name="exchange_id" id="exchange_id" style="width:10%;">
			<?php while ($row = mysql_fetch_array($adExchanges)) {
				 if($_POST['exchange_id'] && $_POST['exchange_id']==$row['dsp_portal_name']) { ?>
				 <option selected="selected" value='<?php echo $row['dsp_portal_name'];?>'><?php echo ucfirst($row['dsp_portal_name']);?></option>
				 <?php } else {?>
				<option value='<?php echo $row['dsp_portal_name'];?>'><?php echo ucfirst($row['dsp_portal_name']);?></option>
			<?php } } ?>
		</select> 
        <select name='period_preset' id='period_preset' onchange='periodFormChange(1)' tabindex='1'>
            <option value='today' selected='selected'>Today</option>

            <option value='yesterday' <?php if($_period == "yesterday") : ?> selected="selected" <?php endif; ?> >Yesterday</option>
            
            <option value='last_7_days' <?php if($_period == "last_7_days") : ?> selected="selected" <?php endif; ?> >Last 7 days</option>
            <option value='this_month' <?php if($_period == "this_month") : ?> selected="selected" <?php endif; ?> >This month</option>
            <option value='last_month' <?php if($_period == "last_month") : ?> selected="selected" <?php endif; ?> >Last month</option>

            <option value='all_stats' <?php if($_period == "all_stats") : ?> selected="selected" <?php endif; ?> >All statistics</option>
            <option value='specific' <?php if($_period == "specific") : ?> selected="selected" <?php endif; ?> >Specific dates</option>
        </select>


       <label for='period_start' style='margin-left: 1em'></label>
        <input class="date" name="period_start" id="period_start" type="text" value="<?php  if(!empty($start)){ echo $start;}else{ echo date('Y-m-d');}?>" tabindex="2"/>
        <input type='image' src='assets/images/icon-calendar-d.gif' id='period_start_button' align='absmiddle' border='0' tabindex='3' />
        <label for='period_end' style='margin-left: 1em'> </label>

        <input class="date" name="period_end" id="period_end" type="text" value="<?php if(!empty($end)){ echo $end;}else{ echo date('Y-m-d');} ?>" tabindex="4" /> 
<input type='image' src='assets/images/icon-calendar-d.gif' id='period_end_button' align='absmiddle' border='0' tabindex='3' />
	<a href='' onclick='return periodFormSubmit()'>
        <img src='assets/images/ltr/go_blue.gif' border='0' tabindex='6' /></a>
	
</form>


<style type="text/css">


table.gridtable {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	width: 100%;
	border-color: #999999;
	border-collapse: collapse;
}
table.gridtable th {
	
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #999999;
}
table.gridtable td {
	
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #999999;
}

</style>


<?php
if(OA_Dal_Delivery_numRows($stat)>0)
{	
	?>
	<link rel="stylesheet" type="text/css" href="../../assets/min.php?g=oxp-css-ltr&v=2.8.10">

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr>
             <td style="padding-bottom:5px; white-space: nowrap;">
				<br />
				<a href="oxm_dspexport.php?start=<?php echo $startdate;?>&end=<?php echo $endate;?>&exstart=<?php echo $ex;?>" accesskey="e">
				<img src="assets/images/excel.gif" border="0" alt="" /> <u>E</u>xport Statistics to Excel</a>
            </td>
            
            <td width="90%">&nbsp;</td>
         </tr>
</table>
     
        <table width="100%" class="table">
       <tr>
                  		<th scope="col" class="aleft" style="color: #0767A8;">DSP Portal</th>    
                		<th scope="col" class="aright" style="color: #0767A8;">Bid Request</th>                                              
			    	<th scope="col" class="aright" style="color: #0767A8;">Bid Response</th>
			    	<th scope="col" class="aright" style="color: #0767A8;">Won Response</th>
			    	<th scope="col" class="aright" style="color: #0767A8;">Advertiser Bid($)</th>
			  	<th scope="col" class="aright" style="color: #0767A8;">Won Price($)</th>  
				<!--<th scope="col" class="aright" style="color: #0767A8;">Admin Share($)</th> --> 

            
	</tr> 
<?php

while($value	=	OA_Dal_Delivery_fetchAssoc($stat))
{		
//$exchangename=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select * from rv_dj_dsp where id='".$value['exchange_id']."'"));
//	print_r($reqcounts);
	//die();
	foreach($reqcounts as $key=>$list):  
		$value['req_count']	=	$list['req'];	
		$value['res_count']	=	$list['res'];
	endforeach;
	$exchangename=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select * from rv_dj_dsp where id='".$reqcounts[0]['exchange_id']."'"));
?>
	
<tr>
<?php if(empty($exchangename['dsp_portal_name']))
{ ?><td class="aright dark" ><?php echo '-'; ?></td>
<?php } else { ?> <td class="aright dark"><a href="oxm_dspdaystat.php?start=<?php echo $start;?>&end=<?php echo $end;?>&period=<?php echo $_period; ?>&exchangeid=<?php echo $value['exchange_id']; ?>"><?php echo $exchangename['dsp_portal_name'];?></td></a></td>
<?php }?>

<?php if($value['req_count']==0)
{ ?><td class="aright dark" ><?php echo '-'; ?></td>
<?php } else { ?> <td class="aright dark" ><?php echo $value['req_count']; ?></td>
<?php }?>

<?php if($value['res_count']==0)
{ ?><td class="aright dark" ><?php echo '-'; ?></td>
<?php } else { ?> <td class="aright dark" ><?php echo $value['res_count']; ?></td>
<?php }?>

<?php if($value['win_count']==0)
{ ?><td class="aright dark" ><?php echo '-'; ?></td>
<?php } else { ?> <td class="aright dark" ><?php echo $value['win_count']; ?></td>
<?php }?>

<?php if($value['bid_price']==0)
{ ?><td class="aright dark" ><?php echo '-'; ?></td>
<?php } else { ?> <td class="aright dark" ><?php echo number_format($value['bid_price']/1000,3); ?></td>
<?php }?>

<?php if($value['win_price']==0)
{ ?><td class="aright dark" ><?php echo '-'; ?></td>
<?php } else { ?> <td class="aright dark" ><?php echo number_format($value['win_price']/1000,3); ?></td>
<?php }?>
</tr>
<?php } ?>

<?php }	

else{
echo '<div style="margin-top: 2em;font-size:12px;" class="errormessage"><img border="0" align="absmiddle" width="16" height="16" src="../../assets/images/info.gif" class="errormessage">There are currently no statistics available for '.ucfirst($_POST["exchange_id"]).' the period '.$start.' to '.$end.'</div>';
}
@mysql_close($GLOBALS['_MAX']['ADMIN_DB_LINK']);
?>

        <script type='text/javascript'>
        <!--
        Calendar.setup({
            inputField : 'period_start',
            ifFormat   : '%Y-%m-%d',
            button     : 'period_start_button',
            align      : 'Bl',
            weekNumbers: false,
            firstDay   : 1,
            electric   : false
        });
        Calendar.setup({
            inputField : 'period_end',
            ifFormat   : '%Y-%m-%d',
            button     : 'period_end_button',
            align      : 'Bl',
            weekNumbers: false,
            firstDay   : 1,
            electric   : false
        });

       
        //-->


 function periodFormChange(bAutoSubmit)
  {
 	    var o = document.getElementById('period_preset');
        var periodSelectName = o.options[o.selectedIndex].value;
        var specific = periodSelectName == 'specific';
    	var advertiser = periodSelectName == 'advertiser';
 	    var periodTabIndex = 2;
	    document.getElementById('period_start').readOnly = !specific;
        document.getElementById('period_start_button').disabled = !specific;
        document.getElementById('period_end').readOnly = !specific;
        document.getElementById('period_end_button').disabled = !specific;
        document.getElementById('period_start').style.backgroundColor = '#FFFFFF';
        document.getElementById('period_end').style.backgroundColor = '#FFFFFF';
        document.getElementById('period_start').tabIndex = periodTabIndex;
        document.getElementById('period_start_button').tabIndex = periodTabIndex + 1;
        document.getElementById('period_end').tabIndex = periodTabIndex + 2;
        document.getElementById('period_end_button').tabIndex = periodTabIndex + 3;
	    document.getElementById('period_start_button').src = 'assets/images/icon-calendar.gif';
        document.getElementById('period_end_button').src = 'assets/images/icon-calendar.gif' ;
		document.getElementById('period_start_button').readOnly = !specific;
        document.getElementById('period_end_button').readOnly = !specific;
        document.getElementById('period_start_button').style.cursor = specific ? 'auto' : 'default';
        document.getElementById('period_end_button').style.cursor = specific ? 'auto' : 'default';
          
}
periodFormChange(0);

function periodFormSubmit() 
{
			var form = document.getElementById('period_preset').form;
            if (checkDates(form)) {
              form.submit();
            }
            return false;
}

function checkDates(form)
{
          var startField = form.period_start;
          var endField = form.period_end;

          if (!startField.disabled && startField.value != '') {
            var start = Date.parseDate(startField.value, '%Y-%m-%d');
          }
          if (!startField.disabled && endField.value != '') {
            var end = Date.parseDate(endField.value, '%Y-%m-%d');
          }

         
          return true;
}

 </script>

<?php

//_echoDeliveryCappingJs();

phpAds_PageFooter();

?>

