<?php

$s2s[7] = 'http://pixel.leadzu.com/pixel.php?service=1318&hash={pid1}';
$s2s[8] = 'http://click.ezmob.com/traffic/pixel/8df02755ef8c4f9289b3deef67995ab1/?mytoken={pid1}';
$s2s[10] = 'http://cb.adtwirl.com/callback.php?subid={pid1}';
$s2s[11] = 'http://pixel.leadzu.com/pixel.php?service=1318&hash={pid1}';
$s2s[19] = 'http://mobistein.com/app/conversion?jp={pid1}';
$s2s[22] = 'http://ketads.com/site/postback?affiliate_id={affiliate_id}&transaction_id={pid1}';
$s2s[24] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';
$s2s[25] = 'http://www.securebill.mobi/bg.php?clickID={pid1}&idcallback=11325883f727b7565fc495ddf786e79e';
$s2s[26] = 'http://mobistein.com/app/conversion?jp={pid1}';
$s2s[28] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';
$s2s[29] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';
$s2s[31] = 'http://track.globaltrackads.com/aff_lsr?transaction_id={pid1}&security_token=e75bbaa8e39a282d8e4193dc12b8e275';
$s2s[32] = 'http://adserver.kimia.es/conversion_get/pixel.jpg?kp={pid1}';
$s2s[33] = 'http://freepaisa.co/v1/callback89?click={pid1}';
$s2s[34] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';
if(strlen($s2s[$results['tracker_id']])>1) {
/*
$split_count = substr_count($results['publisher_tid'],'|');
	
	if($split_count==0) {

    			$s2s_url = $s2s[$results['tracker_id']].$results['publisher_tid'];

	} else{
		        list($tid_1,$tid_2) = explode('|',$results['publisher_tid']);
			$s2s_url = str_ireplace('{tid1}',$tid_1,$s2s[$results['tracker_id']]);
			$s2s_url = str_ireplace('{tid2}',$tid_2,$s2s_url);
			
	}
	
*/

    $s2s_url = str_ireplace('{pid1}',$results['publisher_tid'],$s2s[$results['tracker_id']]);	
    $s2s_url = str_ireplace('{pid2}',$results['publisher_tid2'],$s2s_url);	 
    $s2s_output = file_get_contents($s2s_url);
    $log_data = date("Y-m-d G:i:s")."#".$results['tracker_id']."#".$results['publisher_tid']."#".$s2s_url."#".$s2s_output."\n";
    
    
    $handle = fopen('../log/s2s_log_'.$results['tracker_id'].'_'.date("Y-m-d").'.txt', 'a+');
    fwrite($handle, $log_data);
    fclose($handle);
    
    //file_put_contents('../log/s2s_log_'.date("Y-m-d").'.txt', $s2s_output, FILE_APPEND);
}



?>
