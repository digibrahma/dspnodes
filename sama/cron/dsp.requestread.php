<?php
set_time_limit(0);
date_default_timezone_set('UTC');
include "../incs/db.php";
include "../incs/constants.php";
define(PATH_TO_DSP_LOGS,"/usr/local/apps/apache2/www/htdocs/logs/dsp");
$get_exiting_livekpi = mysqli_query($dbLink,"select max(date) as date from sama_livemis where type='DSP_REQ'") or die(mysqli_error());
$got_existing_livekpi = mysqli_fetch_array($get_exiting_livekpi);



$get_exiting_dsps = mysqli_query($dbLink,"SELECT id FROM `rv_dj_dsp` where status=1") or die(mysqli_error());


$GetLastDateTime = $got_existing_livekpi['date'];

$hr_string = date("Y-m-d_H",strtotime($GetLastDateTime)+3600);
$files=array();

$i=1;
$Now = date("Y-m-d H:00:00",strtotime($GetLastDateTime)+3600);

while($got_existing_dsps = mysqli_fetch_array($get_exiting_dsps)) {
    
    $file = 'dsp.'.$got_existing_dsps['id'].'.'.$hr_string.'.log';
    
    if(file_exists(PATH_TO_DSP_LOGS.'/'.$file)) {
	$COUNT = intval(exec("wc -l ".PATH_TO_DSP_LOGS."/".$file));
	$query[$i++] = "insert into sama_livemis (date, dsp, type, value) values ('".$Now."','".$got_existing_dsps['id']."','DSP_REQ','".$COUNT."')";

    }
    
}


//print_r($query);exit;
// DSP REQUESTS



foreach($query as $id=>$Query) {
	mysqli_query($dbLink,$Query) or die(mysqli_error());
	echo "Executed Query #:".$id;
}

?>