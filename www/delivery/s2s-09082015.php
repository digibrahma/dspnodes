<?php

$s2s[7] = 'http://pixel.leadzu.com/pixel.php?service=1318&hash=';
$s2s[8] = 'http://click.ezmob.com/traffic/pixel/8df02755ef8c4f9289b3deef67995ab1/?mytoken=';
$s2s[10] = 'http://cb.adtwirl.com/callback.php?subid=';
$s2s[11] = 'http://pixel.leadzu.com/pixel.php?service=1318&hash=';
$s2s[19] = 'http://mobistein.com/app/conversion?jp=';
$s2s[22] = 'http://ketads.com/site/postback?affiliate_id={affiliate_id}&transaction_id=';
$s2s[24] = 'https://affle.co/global/activation.php?af_cid={tid1}&af_tid={tid2}';

if(strlen($s2s[$results['tracker_id']])>1) {
    

$split_count = substr_count($results['publisher_tid'],'|');
	
	if($split_count==0) {

    			$s2s_url = $s2s[$results['tracker_id']].$results['publisher_tid'];

	} else{
		        list($tid_1,$tid_2) = explode('|',$results['publisher_tid']);
			$s2s_url = str_ireplace('{tid1}',$tid_1,$s2s[$results['tracker_id']]);
			$s2s_url = str_ireplace('{tid2}',$tid_2,$s2s_url);
			
	}
    
    $s2s_output = file_get_contents($s2s_url);
    $log_data = date("Y-m-d G:i:s")."#".$results['tracker_id']."#".$results['publisher_tid']."#".$s2s_url."#".$s2s_output."\n";
    
    
    $handle = fopen('../log/s2s_log_'.$results['tracker_id'].'_'.date("Y-m-d").'.txt', 'a+');
    fwrite($handle, $log_data);
    fclose($handle);
    
    //file_put_contents('../log/s2s_log_'.date("Y-m-d").'.txt', $s2s_output, FILE_APPEND);
}



?>