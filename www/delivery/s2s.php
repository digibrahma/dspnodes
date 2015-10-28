<?php


$s2s[12] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';
$s2s[13] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';
$s2s[15] = 'https://affle.co/global/activation.php?af_cid={pid1}&af_tid={pid2}';

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
    $log_data = date("Y-m-d G:i:s")."#".$results['tracker_id']."#".$results['publisher_tid']."#".$s2s_url."#".$s2s_output.PHP_EOL;
    
    
    $handle = fopen('../../logs/s2s.outgoing.'.$results['tracker_id'].'.'.date("Y-m-d").'.log', 'a+');
    fwrite($handle, $log_data);
    fclose($handle);
    
    //file_put_contents('../log/s2s_log_'.date("Y-m-d").'.txt', $s2s_output, FILE_APPEND);
}



?>