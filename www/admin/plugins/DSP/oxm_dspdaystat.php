<?php

require_once '../../../../init.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
require_once LIB_PATH . '/Plugin/PluginManager.php';
require_once LIB_PATH . '/Plugin/ComponentGroupManager.php';
phpAds_PageHeader('dsp-statistics');
 
 
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN,OA_ACCOUNT_TRAFFICKER,OA_ACCOUNT_MANAGER,OA_ACCOUNT_ADVERTISER);


$oPluginManager = new OX_PluginManager();
$oComponentGroupManager = new OX_Plugin_ComponentGroupManager();
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid'] = $clientid;
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid] = $campaignid;
phpAds_SessionDataStore();


$pageName = basename($_SERVER['SCRIPT_NAME']);
$tabindex = 1;

$st=htmlspecialchars($_GET["start"]);
$e=htmlspecialchars($_GET["end"]);
$period_cat=htmlspecialchars($_GET["period"]);  

	$_period = !empty($_POST['period_preset']) ? $_POST['period_preset'] : $period_cat;
    $name_val=!empty($_POST['statsBreakdown']) ? $_POST['statsBreakdown'] : 'day';
if($_POST['statsBreakdown']!='')
 {

	if($_POST['statsBreakdown']=='banner')
    	{
		
		$list_view='banner';
		$name='banner';
	    $src="<img src='assets/images/icon-campaign.gif'>";
    	}				
    			
    
}	

else
{
	
	$list_view='banner';
	$name='banner';
	$src="<img src='assets/images/icon-date.gif'>";
}	



if($_POST['period_preset']!='')
{

	if($_POST['period_preset']=='today')
	{
			$start=date("Y-m-d 00:00:00");//$date=date("Y-m-d H:00:00");
			$end=date("Y-m-d H:00:00");
			
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1]; 
		
		
	}
	else if($_POST['period_preset']=='yesterday')
	{
		$start=date('Y-m-d 00:00:00', strtotime('Yesterday'));
		$end=date('Y-m-d 23:59:59', strtotime('Yesterday'));

		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
	
	}

	else if($_POST['period_preset']=='last_7_days')
	{
		$start=date('Y-m-d 00:00:00', strtotime('Today - 7 Day'));
		$end=date('Y-m-d 23:59:59', strtotime('Today - 1 Day'));
		
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
	}
	else if($_POST['period_preset']=='this_month')
	{
		$start=date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), 1, date('Y')));
		$end=date('Y-m-d 23:59:59');
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
	}
	else if($_POST['period_preset']=='last_month')
	{
		$start=date('Y-m-d 00:00:00', mktime(0, 0, 0, (date('m') - 1), 1, date('Y')));
		$end=date('Y-m-d 23:59:59', mktime(0, 0, 0, date('m'), 0, date('Y')));
		$result=time_zone($start,$end);
			
		$startdate=$result[0];
		$endate=$result[1];
	}
	else if($_POST['period_preset']=='all_stats')
	{
		$start=date("0000-00-00 00:00:00");
		$end=date("Y-m-d 24:00:00");
		
		$result=time_zone($start,$end);
			
		$startdate=date("0000-00-00 00:00:00");
		$endate=date("Y-m-d 24:00:00");
	}
	else if($_POST['period_preset']=='specific')
	{
					
		$start=$_POST['period_start'];
		$end=$_POST['period_end'];
		

	    $startdate=date("$start 00:00:00");
	   
		//$startdate=$result[0];
		//$endate=$result[1];
			$endate=date("$end 24:00:00");
	
	}
		

}

else
{
  $period_cat=$_GET['period'];
  $st=$_GET['start'];
  $e=$_GET['end'];
		
		$period=$period_cat;	
			
		$startdate=$st;
		$endate=$e ;
}


//////////timezone


function time_zone($start,$end)
{
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
$timezone_query =OA_Dal_Delivery_query("select preference_id  from ".$table_prefix."preferences where preference_name='timezone'");
$timezone_row = OA_Dal_Delivery_fetchAssoc($timezone_query);
$timezone_id = $timezone_row['preference_id'];

$sess = $GLOBALS['session']['user'];
foreach($sess as $key => $value)
{
$ke[]=$key;
$val[]=$value;
}
$acc = $val['1']['account_id'] ;

		$query = OA_Dal_Delivery_query("select value from ".$table_prefix."account_preference_assoc where preference_id='".$timezone_id."' AND account_id='".$acc."'");
		$row = OA_Dal_Delivery_fetchAssoc($query);
		$timezone = $row['value'];
	
		$value_query = OA_Dal_Delivery_query("select value from ".$table_prefix."dj_timezone where timezone='".$timezone."' ");
		$value_row = OA_Dal_Delivery_fetchAssoc($value_query);
		$offset = $value_row['value'];

		$offsetarr = explode('.',$offset);
		$hour = $offsetarr['0'];
		$minute = $offsetarr['1'];
		
		$start = strtotime($start);
		
		
	   if($hour<0)
	   {
		  
			$start = $start + ($hour * 60 - $minute) * 60;  //-4 h 30 m = -270
			
	    }		
		else
		{
			
			$start = $start +($hour * 60 + $minute) * 60;  //4 h 30 m +270
		}
			$start_date=date("Y-m-d H:i:s",$start);
				
				
		$end = strtotime($end);	
			
		  if($hour<0)
	   {
		  $end = $end + ($hour * 60 - $minute) * 60;  //-4 h 30 m = -270
			
	    }		
		else
		{
			
			$end = $end +($hour * 60 + $minute) * 60;  //4 h 30 m +270
		}	
			
			$end_date=date("Y-m-d H:i:s",$end);	
	
			
$date[]=$start_date;
$date[]=$end_date;

	return $date;

	
}


			$cond='';

			if(!empty($_REQUEST['exchangeid']))
			{ 
				$cond="and adx.id=".$_REQUEST['exchangeid']."";
			}


 $agency_id = OA_Permission::getEntityId();

	if($clientid)
	{
			$conf 		= 	$GLOBALS['_MAX']['CONF']['database'];
			$host		=	$conf['host'];
			$db			=	$conf['name'];	
			$dbh 		= 	new PDO("mysql:host=$host;dbname=$db", $conf['username'],  $conf['password']) or die(mysql_error());
			$sql 		= 	"call getRepResClient('".$startdate."','".$endate."','".$clientid."')";
			$statment	=	$dbh->query($sql);
			$reqcounts 	=	$statment->fetchAll(PDO::FETCH_ASSOC);
			$dbh 		= 	null;	
			$val	=	OA_Dal_Delivery_query("SELECT req.exchange_id as exchange_id,IFNULL(COUNT(win.id),0) as win_count,
										IFNULL(SUM(win.price),0) as win_price, IFNULL(SUM(res.advertiser_bid_price),0)
										as bid_price, 
										oxc.campaignname as camp_name, 
										oxb.description as ban_name,
										oxcl.clientname as client_name
										FROM rv_dj_dsp_win_notice as win 
										left join rv_dj_dsp_response as res on res.requset_id= win.request_id 
										left join rv_dj_dsp_bid_request req on req.id=res.requset_id 
										LEFt JOIN rv_banners as oxb ON oxb.bannerid=res.adid 
										LEFT JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
										LEFt JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
										LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 
										WHERE 1 AND DATE(res.datetime) between  '$startdate' and '$endate' and oxcl.clientid=$clientid   AND DATE(win.datetime) between '$startdate' and '$endate'AND DATE(res.datetime) between '$startdate' and '$endate' 
										GROUP BY DATE(req.datetime),req.exchange_id
										ORDER BY DATE(req.datetime) DESC");
					/*$val=OA_Dal_Delivery_query("SELECT
						 r.client_name,
						 r.camp_name,
						 r.ban_name,
						 IFNULL(COUNT(w.win_count),0) as win_count,
						 IFNULL(SUM(w.win_price),0) as win_price,
						 IFNULL(COUNT(req.id),0) as req_count,
						 IFNULL(COUNT(r.res_count),0) as res_count,
						 IFNULL(SUM(r.bid_price),0) as bid_price,
						 IFNULL(SUM(r.sbid_price),0) as sbid_price,
						 IFNULL(SUM(r.admin_price),0) as admin_price
					FROM 
						(
						SELECT 
							oxcl.clientname as client_name, 
							oxc.campaignname as camp_name, 
							oxb.description as ban_name,
							DATE(res.datetime) as datetime,
							res.adid,
							res.campaign_id,
							res.requset_id as request_id,
							res.response_id as res_count, 
							res.advertiser_bid_price as bid_price, 
							res.smaato_bid_price as sbid_price, 
							res.admin_rev as admin_price
						FROM 
							rv_dj_dsp_response as res 
						JOIN 
							rv_banners as oxb ON oxb.bannerid=res.adid
						JOIN 
							rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
						JOIN 
							rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
						WHERE 1 
							AND DATE(res.datetime) between '".$startdate."' and '".$endate."' and oxcl.clientid='$clientid'
					) as r 
					LEFT JOIN  
					(
						SELECT 
							oxcl.clientname as client_name, 
							oxc.campaignname as camp_name, 
							oxb.description as ban_name,
							win.adid,
							win.request_id,
							win.id as win_count, 
							win.price as win_price
						FROM 
							rv_dj_dsp_win_notice as win 
						JOIN 
							rv_banners as oxb ON oxb.bannerid=win.adid
						JOIN 
							rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
						JOIN 
							rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
						WHERE 1 
							AND DATE(win.datetime) between '".$startdate."' and '".$endate."' and oxcl.clientid='$clientid'
					) as w ON w.request_id=r.request_id 
					LEFT JOIN rv_dj_dsp_bid_request as req ON req.id=r.request_id 					
					LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 					
					WHERE 1 $cond
						AND DATE(r.datetime) between '".$startdate."' and '".$endate."'
						GROUP BY r.adid 
						ORDER BY r.adid DESC");*/
		
		
	
	}
	else
		{	

		if($agency_id==0) // Admin Stat
		{
			$conf 		= 	$GLOBALS['_MAX']['CONF']['database'];
			$host		=	$conf['host'];
			$db			=	$conf['name'];	
			$dbh 		= 	new PDO("mysql:host=$host;dbname=$db", $conf['username'],  $conf['password']) or die(mysql_error());
			$sql 		= 	"call getRepResAdminByPortal('".$startdate."','".$endate."','".$_REQUEST['exchangeid']."')";
			$statment	=	$dbh->query($sql);
			$reqcounts 	=	$statment->fetchAll(PDO::FETCH_ASSOC);
			$dbh 		= 	null;
			
			/*$reqcount=OA_Dal_Delivery_query("SELECT count(a.id) as req,count(b.response_id) as res,a.exchange_id 
												FROM rv_dj_dsp_bid_request a 
												left join `rv_dj_dsp_response` b on (b.requset_id= a.id and b.datetime=a.datetime) left JOIN rv_banners as oxb ON oxb.bannerid=b.adid 
												left JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid 
												left JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=a.exchange_id 
												where DATE(a.datetime) between '$startdate' and '$endate' and adx.id='".$_REQUEST['exchangeid']."' group by a.exchange_id ,oxb.bannerid");	*/			

			$val	=	OA_Dal_Delivery_query("SELECT req.exchange_id as exchange_id,
												IFNULL(COUNT(win.id),0) as win_count,
												IFNULL(SUM(win.price),0) as win_price,
												IFNULL(SUM(res.advertiser_bid_price),0) as bid_price,
												oxc.campaignname as camp_name, 
												oxb.description as ban_name
												FROM rv_dj_dsp_win_notice as win 
												left join rv_dj_dsp_response as res on res.requset_id= win.request_id 
												left join rv_dj_dsp_bid_request req on req.id=res.requset_id 
												LEFt JOIN rv_banners as oxb ON oxb.bannerid=res.adid 
												LEFT JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
												LEFt JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 
												WHERE 1 AND DATE(res.datetime) between  '$startdate' and '$endate' AND 
												DATE(win.datetime) between '$startdate' and '$endate' AND DATE(res.datetime) between '$startdate' and '$endate' and adx.id='".$_REQUEST['exchangeid']."'
												GROUP BY DATE(req.datetime),req.exchange_id,oxb.bannerid
												ORDER BY DATE(req.datetime) DESC");
	
		}
		else// Manager stat.
		{ 
				
				$conf 		= 	$GLOBALS['_MAX']['CONF']['database'];
				$host		=	$conf['host'];
				$db			=	$conf['name'];	
				$dbh 		= 	new PDO("mysql:host=$host;dbname=$db", $conf['username'],  $conf['password']) or die(mysql_error());
				$sql 		= 	"call getRepResByManager('".$startdate."','".$endate."','".$_REQUEST['exchangeid']."','".$agency_id."')";
				$statment	=	$dbh->query($sql);
				$reqcounts 	=	$statment->fetchAll(PDO::FETCH_ASSOC);
				$dbh 		= 	null;
				/*$query1	="SELECT count(a.id) as req,count(b.response_id) as res,a.exchange_id 
												FROM rv_dj_dsp_bid_request a 
												left join `rv_dj_dsp_response` b on (b.requset_id= a.id and b.datetime=a.datetime) left JOIN rv_banners as oxb ON oxb.bannerid=b.adid 
												left JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid 
												left JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=a.exchange_id 
												where DATE(a.datetime) between '$startdate' and '$endate' and adx.id='".$_REQUEST['exchangeid']."' and oxcl.agencyid='$agency_id' group by a.exchange_id";
				$reqcount=OA_Dal_Delivery_query($query1);	*/
				
				$query2	=	"SELECT req.exchange_id as exchange_id,
												IFNULL(COUNT(win.id),0) as win_count,
												IFNULL(SUM(win.price),0) as win_price,
												IFNULL(SUM(res.advertiser_bid_price),0) as bid_price,
												oxc.campaignname as camp_name, 
												oxb.description as ban_name
												FROM rv_dj_dsp_win_notice as win 
												left join rv_dj_dsp_response as res on res.requset_id= win.request_id 
												left join rv_dj_dsp_bid_request req on req.id=res.requset_id 
												LEFt JOIN rv_banners as oxb ON oxb.bannerid=res.adid 
												LEFT JOIN rv_campaigns as oxc ON oxc.campaignid=oxb.campaignid
												LEFt JOIN rv_clients as oxcl ON oxcl.clientid=oxc.clientid 
												LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 
												WHERE 1 AND DATE(res.datetime) between  '$startdate' and '$endate' AND DATE(win.datetime) between '$startdate' and '$endate' AND DATE(res.datetime) between '$startdate' and '$endate' and adx.id='".$_REQUEST['exchangeid']."'
												and oxcl.agencyid='$agency_id' GROUP BY DATE(req.datetime),req.exchange_id,req.exchange_id,oxb.bannerid
												ORDER BY DATE(req.datetime) DESC";			

				$val	=	OA_Dal_Delivery_query($query2);
							
					
								
		}
	
	
	
	}

?>


	

<form method='post'>
        <select name='period_preset' id='period_preset' onchange='periodFormChange(1)' tabindex='1'>
            <option value='today' selected='selected'>Today</option>

            <option value='yesterday' <?php if($_period == "yesterday") : ?> selected="selected" <?php endif; ?> >Yesterday</option>
            
            <option value='last_7_days' <?php if($_period == "last_7_days") : ?> selected="selected" <?php endif; ?> >Last 7 days</option>
            <option value='this_month' <?php if($_period == "this_month") : ?> selected="selected" <?php endif; ?> >This month</option>
            <option value='last_month' <?php if($_period == "last_month") : ?> selected="selected" <?php endif; ?> >Last month</option>

            <option value='all_stats' <?php if($_period == "all_stats") : ?> selected="selected" <?php endif; ?> >All statistics</option>
            <option value='specific' <?php if($_period == "specific") : ?> selected="selected" <?php endif; ?> >Specific dates</option>

        </select>
        
         <!-- <select name='statsBreakdown' id='statsBreakdown'>
            <option value='select' selected='selected'>Select</option>
                <option value='banner' <?php if($name_val == "banner") : ?> selected="selected" <?php endif; ?> >Banners</option>
        </select>-->
            
            


			
			

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





	<link rel="stylesheet" type="text/css" href="../../assets/min.php?g=oxp-css-ltr&v=2.8.10">

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr>
             <td style="padding-bottom:5px; white-space: nowrap;">
				<br />
				<a href="oxm_dspday_export.php?start=<?php echo $startdate;?>&end=<?php echo $endate;?>&exstart=<?php echo $ex;?>&view=<?php echo $list_view; ?>&period=<?php echo $period; ?>&exchangeid=<?php echo $_REQUEST['exchangeid']; ?>" accesskey="e">
				<img src="assets/images/excel.gif" border="0" alt="" /> <u>E</u>xport Statistics to Excel</a>
            </td>
            
            <td width="90%">&nbsp;</td>
         </tr>
</table>
 
    <?php if(OA_Dal_Delivery_numRows($val)>0){ ?>
        <table width="100%" class="table">
       <tr>
                <th scope="col" class="aleft" style="color: #0767A8;">Campaign Name</th>    
                <th scope="col" class="aleft" style="color: #0767A8;">Banner Name</th>                
			     <th scope="col" class="aright" style="color: #0767A8;">Bid Response</th>  
				<th scope="col" class="aright" style="color: #0767A8;">Won Response</th>  
				
               <th scope="col" class="aright" style="color: #0767A8;">Advertiser Bid($)</th>  
               <th scope="col" class="aright" style="color: #0767A8;">Won Price($)</th> 
               <!--<th scope="col" class="aright" style="color: #0767A8;">Admin Share($)</th> -->

	</tr> 

 <?php  
   
			$i=0;
	        while($row = OA_Dal_Delivery_fetchAssoc($val))
                  {
						/*foreach($reqcounts as $key=>$list): 
							$row['res_count']	=	$list['res'];
						endforeach;*/
						$row['res_count']	=	$reqcounts[$i++]['res'];
					    $admin_share=$row['bid_price']-$row['win_price']					  
					
		?>
			
					<tr>
		<td class="aleft dark" style="color: #0767A8;"><?php echo $row['camp_name']; ?></td>
		<td class="aleft dark" style="color: #0767A8;"><?php echo $row['ban_name'];?></td>			
		<td class="aright dark" ><?php echo $row['res_count']; ?></td>
		<td class="aright dark" ><?php echo $row['win_count']; ?></td>
		<td class="aright dark" ><?php echo number_format($row['bid_price']/1000,3); ?></td>
		<td class="aright dark" ><?php echo number_format($row['win_price']/1000,3); ?></td> 
		
		<!--<td class="aright dark" ><?php echo $admin_share; ?></td>-->

					</td>	 	
		</tr>
				
				
		
				
		<?php
				
		}
			    
        


echo '</table>';
}
else{
echo '<div style="margin-top: 2em;" class="errormessage"><img border="0" align="absmiddle" width="16" height="16" src="../../assets/images/info.gif" class="errormessage">There are currently no statistics available for the period '.$start.' to '.$end.'</div>';
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

    function periodFormSubmit() {
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

