<?php
 
include "ipdivert.php";

 $log_data = date("Y-m-d G:i:s")."#".$user_ip."#".serialize($_REQUEST)."\n";
 $handle = fopen('../www/log/sitescout_log_'.date("Y-m-d").'.txt', 'a+');
 fwrite($handle, $log_data);
 fclose($handle);
   


$campaign_id = $_REQUEST['campaign_id'];
$nocampaign_id = $_REQUEST['nocampaign_id'];
$zone_id = $_REQUEST['zone_id'];
$pid = $_REQUEST['pid'];
$imprtrack = $_REQUEST['imprtrack'];

$ck_url = 'http://rig.digibrahma.in/www/delivery/ck.php?oaparams=2__bannerid={bannerid}&zoneid='.$zone_id.'__pid='.$pid.'__cb=e874d3fa3f__transaction_id={transaction_id}';
$impr_url = 'http://rig.digibrahma.in/www/delivery/lg.php?bannerid={bannerid}&zoneid='.$zone_id;




$get_campaign_banner_urls = mysqli_query($connxn,"select bannerid from rv_banners where campaignid='".$campaign_id."' and lower(statustext)='".$FoundCarrier."' limit 1") or die(mysqli_error($connxn));
$get_nocampaign_banner_urls = mysqli_query($connxn,"select bannerid from rv_banners where campaignid='".$nocampaign_id."' and lower(statustext)='nocampaign' limit 1") or die(mysqli_error($connxn));
$result_campaign = mysqli_fetch_array($get_campaign_banner_urls);
$result_nocampaign = mysqli_fetch_array($get_nocampaign_banner_urls);


if($FoundCarrier != false) {
    
    $redirect_url = str_replace("{bannerid}",$result_campaign['bannerid'],$ck_url);
    $impression_url = str_replace("{bannerid}",$result_campaign['bannerid'],$impr_url); 
} else{
    $redirect_url = str_replace("{bannerid}",$result_nocampaign['bannerid'],$ck_url);
    $impression_url = str_replace("{bannerid}",$result_nocampaign['bannerid'],$impr_url);
}

if($imprtrack==1) {
    $URL = $impression_url;
} else{
    $URL = $redirect_url;
}


    header("Location: ".$URL);exit;

?>