<?php

$s2s[7] = 'http://pixel.leadzu.com/pixel.php?service=1318&hash=';
$s2s[8] = 'http://click.ezmob.com/traffic/pixel/8df02755ef8c4f9289b3deef67995ab1/?mytoken=';
$s2s[11] = 'http://pixel.leadzu.com/pixel.php?service=1318&hash=';

if(strlen($s2s[$results['tracker_id']])>1) {
    
    $s2s_url = $s2s[$results['tracker_id']].$results['publisher_tid'];
    
    $s2s_output = file_get_contents($s2s_url);
    $log_data = date("Y-m-d G:i:s")."#".$results['tracker_id']."#".$results['publisher_tid']."#".$s2s_url."#".$s2s_output."\n";
    
    
    $handle = fopen('../log/s2s_log_'.date("Y-m-d").'.txt', 'a+');
    fwrite($handle, $log_data);
    fclose($handle);
    
    //file_put_contents('../log/s2s_log_'.date("Y-m-d").'.txt', $s2s_output, FILE_APPEND);
}



?>