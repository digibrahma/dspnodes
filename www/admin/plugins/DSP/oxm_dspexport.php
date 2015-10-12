<?php

// Require the initialisation file
require_once '../../../../init.php';


// Include required files
require_once MAX_PATH . '/lib/pear/Spreadsheet/Excel/Writer.php';
//(campaignid,banner,Statustext,Comments,keyword,bannertext,bannerid,	url1,weight1,url2,weight2,url3,weight3,url4,weight4,Ads,Reports

//print_r($_POST['txtCntryName']);
//print_r($_POST['txtCityName']);
require_once MAX_PATH . '/www/admin/config.php';


// Required files
require_once MAX_PATH . '/www/admin/config.php';
require_once LIB_PATH . '/Plugin/PluginManager.php';
require_once LIB_PATH . '/Plugin/ComponentGroupManager.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN,OA_ACCOUNT_TRAFFICKER,OA_ACCOUNT_MANAGER,OA_ACCOUNT_ADVERTISER);


$oPluginManager = new OX_PluginManager();
$oComponentGroupManager = new OX_Plugin_ComponentGroupManager(); $agency_id = OA_Permission::getEntityId();
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$name='Exported Statistics - Dsp'.' '.'Statistics'.' '.'from'.' '.$_GET['start'].' '.'to'.' '.$_GET['end'];

$agency_id = OA_Permission::getEntityId();


$start=$_GET['start'];
$end=$_GET['end'];

$startdate=date($start, strtotime('$start'));
$endate=date($end, strtotime('Yesterday'));

 $agency_id = OA_Permission::getEntityId();
 
	
$bannerid=$_GET['bannerid'];

 $agency_id = OA_Permission::getEntityId();
 
	if($clientid)
	{

		$exdat=OA_Dal_Delivery_query("SELECT
						 req.exchange_id as exchange_id,
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
							AND DATE(res.datetime) between '$startdate' and '$endate' and oxcl.clientid=$clientid
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
							AND DATE(win.datetime) between '$startdate' and '$endate'   and oxcl.clientid=$clientid
					) as w ON w.request_id=r.request_id 
					LEFT JOIN rv_dj_dsp_bid_request as req ON req.id=r.request_id 					
					LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 					
					WHERE 1 
						
						AND DATE(r.datetime) between '$startdate' and '$endate'  
						GROUP BY req.exchange_id 	 
						");

	
	}

	else
		{	

		if($agency_id==0)
		{
					$exdat=OA_Dal_Delivery_query("SELECT
						 IFNULL(COUNT(w.win_count),0) as win_count,
						 IFNULL(SUM(w.win_price),0) as win_price,
						 IFNULL(COUNT(req.id),0) as req_count,
						 DATE(req.datetime) as dbdate,
						 req.exchange_id,
						 IFNULL(COUNT(r.res_count),0) as res_count,
						 IFNULL(SUM(r.bid_price),0) as bid_price,
						 IFNULL(SUM(r.sbid_price),0) as sbid_price,
						 IFNULL(SUM(r.admin_price),0) as admin_price
					FROM 
						rv_dj_dsp_bid_request as req
					LEFT JOIN  
					(
						SELECT 
							DATE(res.datetime) as rdate,
							res.requset_id as request_id,
							IFNULL(COUNT(res.response_id),0) as res_count, 
							IFNULL(SUM(res.advertiser_bid_price),0) as bid_price, 
							IFNULL(SUM(res.smaato_bid_price),0) as sbid_price, 
							IFNULL(SUM(res.admin_rev),0) as admin_price 
						FROM 
							rv_dj_dsp_response as res 
						WHERE 1 
						AND DATE(datetime) BETWEEN '$startdate' AND '$endate' GROUP BY 
							request_id
					) as r ON r.request_id=req.id
					LEFT JOIN  
					(
						SELECT 
							DATE(win.datetime)as wdate, 
							win.request_id,
							IFNULL(COUNT(win.id),0) as win_count, 
							IFNULL(SUM(win.price),0) as win_price 
						FROM 
							rv_dj_dsp_win_notice as win 
						WHERE 1 AND DATE(datetime) BETWEEN '$startdate' AND '$endate'
						 GROUP BY 
							request_id
					) as w ON w.request_id=req.id 


					LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 
					WHERE 1 
					AND DATE(req.datetime) BETWEEN '$startdate' AND '$endate'
					GROUP BY DATE(req.datetime),req.exchange_id
					ORDER BY DATE(req.datetime) DESC ");

					
		}
		
		else
		{

		
					$exdat=OA_Dal_Delivery_query("SELECT
						 req.exchange_id as exchange_id,
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
							AND DATE(res.datetime) between '$startdate' and '$endate' and oxcl.agencyid='$agency_id'
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
							AND DATE(win.datetime) between '$startdate' and '$endate'   and oxcl.agencyid='$agency_id'
					) as w ON w.request_id=r.request_id 
					LEFT JOIN rv_dj_dsp_bid_request as req ON req.id=r.request_id 					
					LEFT JOIN rv_dj_dsp as adx ON adx.id=req.exchange_id 					
					WHERE 1 
						
						AND DATE(r.datetime) between '$startdate' and '$endate'  
						GROUP BY req.exchange_id 	 
						");
		
					
		}
	
	
	
	}
	$workbook = new Spreadsheet_Excel_Writer();
    // sending HTTP headers
    $workbook->send($name . '.xls');
    // Creating a worksheet
    $worksheet =&
	$workbook->addWorksheet($publisherDetails->publisherName.'Report');
   
	$format9=& $workbook->addFormat();
 	
	$format9->setBold(); 
	$format9->setSize(12);
	$format9->setPattern(2);
	$format9->setAlign('left');
	 $format9->setTop(2);
	 $format9->setFgColor('green');  
	
	//$format9->setBottom(2);
	
	
		 $format1=& $workbook->addFormat();
		 $format1->setLeft(2);
		 $format1->setTop(2);
		 $format1->setFgColor('green');
		 $format1->setBold(); 
		 $format1->setSize(12);
		 $format1->setPattern(2);

		  
	     $format2=& $workbook->addFormat();
		 $format2->setRight(2);
		 $format2->setTop(2);
		 $format2->setFgColor('green');
		 $format2->setBold(); 
		 $format2->setSize(12);
		 $format2->setPattern(2);
		 
// the data

		 $format3=& $workbook->addFormat();
		 $format3->setTop(2);
		 
		 $format4=& $workbook->addFormat();
		 $format4->setLeft(2);
		 $format4->setFgColor('silver');
	
		 
		 $format5=& $workbook->addFormat();
		 $format5->setRight(2);
		 $format5->setFgColor('silver');
		 
		  $format6=& $workbook->addFormat();
		 $format6->setFgColor('silver');
		 
		
		 $format10=& $workbook->addFormat();
		 $format10->setLeft(2);
		
		 
		 ///startdate color
		 $format7=& $workbook->addFormat();
		 $format7->setLeft(2);
		  $format7->setFgColor('silver');
		  
		  //end date 
		  $format8=& $workbook->addFormat();
		 $format8->setLeft(2);
		 $format8->setBottom(2);
		 $format8->setFgColor('silver');
	
		 
		 $format11=& $workbook->addFormat();
		 $format11->setRight(2);
		 
		 
		 $format12=& $workbook->addFormat();
		 $format12->setBottom(2);
		 
		 $format13=& $workbook->addFormat();
		 $format13->setLeft(2);
		 $format13->setBottom(2);
		 
		 $format14=& $workbook->addFormat();
		 $format14->setBottom(2);
		 $format14->setRight(2);			
		 	 
		$worksheet->write(1, 1, 'Banner Statistics',$format1);
		$worksheet->write(1, 2, '',$format2);
		
		
if($_GET['exstart']=="")
{
	$_GET['exstart']=$_GET['start'];
}
$exstart=substr($_GET['exstart'], 0, 10);
$exend=substr($_GET['end'], 0, 10);

	// $exstart=date('jS F Y \a\t G:i', strtotime($start));
if($exstart!="0000-00-00")
{
		$worksheet->write(2, 1, 'Start Date',$format7);
		$worksheet->write(2, 2, $exstart,$format11);
		   
		
		$worksheet->write(3, 1, 'End Date',$format8);
		$worksheet->write(3, 2, $exend,$format14);
	 
 }
	
$index = 7;
$rows=OA_Dal_Delivery_numRows($exdat);

if($rows>0)
{
		$worksheet->write(5,1,'Dsp Statistics',$format1);
		$worksheet->write(5,2,'',$format9);
		$worksheet->write(5,3,'',$format9);
		$worksheet->write(5,4,'',$format9);
		$worksheet->write(5,5,' ',$format9);
   		$worksheet->write(5,6,' ',$format9);

		$worksheet->write(6,1,'DSP Portal Name',$format4);
		$worksheet->write(6,2,'Bid Request',$format4);
		$worksheet->write(6,3,'Bid Response',$format4);
		$worksheet->write(6,4,'Won Response',$format6);
		$worksheet->write(6,5,'Advertiser Bid($)',$format6);
		$worksheet->write(6,6,'Won Price($)',$format6);
		


		$i=1;
		while($res=OA_Dal_Delivery_fetchAssoc($exdat))
		{

			$exchangename=OA_Dal_Delivery_fetchAssoc(OA_Dal_Delivery_query("select * from rv_dj_dsp where id='".$res['exchange_id']."'"));
			$admin_share=0;
							
				$worksheet->write($index, 1,$exchangename['dsp_portal_name'],$format10);
				$worksheet->write($index, 2,$res['req_count']);
				$worksheet->write($index, 3,$res['res_count']);
				$worksheet->write($index, 4,$res['win_count']);
				$worksheet->write($index, 5,$res['bid_price']);
				$worksheet->write($index, 6,$res['win_price']);
				
			
			if($rows==$i)
			{		
						
				$worksheet->write($index,1,'',$format13);
				$worksheet->write($index,2,'',$format12); 
				$worksheet->write($index,3,'',$format12); 
				$worksheet->write($index,4,'',$format12); 
				$worksheet->write($index,5,'',$format12); 
				$worksheet->write($index,6,'',$format14); 
			}		 
			 
			  
				
		$index++;
		$i++;
		
		}
		
		
		
		
		
}
else
{
	$worksheet->write(4, 4, 'There are No Statistics available for this Specified time period');
	
}
$workbook->close();

?>
