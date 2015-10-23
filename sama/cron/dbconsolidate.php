<?php
set_time_limit(0);
date_default_timezone_set('UTC');
include "../incs/db.php";

$get_exiting_livekpi = mysqli_query($dbLink,"select max(date) as date from sama_livemis where type !='DSP_REQ'") or die(mysqli_error());
$got_existing_livekpi = mysqli_fetch_array($get_exiting_livekpi);

$GetLastDateTime = $got_existing_livekpi['date'];

$Now = date("Y-m-d G:59:59",time()-3600);

$i=1;
// DSP REQUESTS
//$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), exchange_id, 'DSP_REQ', count(1) FROM `rv_dj_dsp_bid_request` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), exchange_id ORDER BY `datetime` DESC";
//$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), exchange_id, 'DSP_REQ', count(1) FROM `rv_dj_axonix_bid_request` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), exchange_id ORDER BY `datetime` DESC";

$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 6, 'DSP_RESP', count(1) FROM `rv_dj_dsp_response` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";

$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 6, 'DSP_BIDPRICE', sum(advertiser_bid_price)/1000 FROM `rv_dj_dsp_response` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";


$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 7, 'DSP_RESP', count(1) FROM `rv_dj_axonix_response` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";

$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 7, 'DSP_BIDPRICE', sum(advertiser_bid_price)/1000 FROM `rv_dj_axonix_response` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";


$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 6, 'DSP_WIN', count(1) FROM `rv_dj_dsp_win_notice` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";

$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 6, 'DSP_WINPRICE', sum(price)/1000 FROM `rv_dj_dsp_win_notice` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";


$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 7, 'DSP_WIN', count(1) FROM `rv_dj_axonix_win_notice` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";

$query[$i++] = "insert ignore into sama_livemis (date, dsp, type, value) SELECT DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00'), 7, 'DSP_WINPRICE', sum(price)/1000 FROM `rv_dj_axonix_win_notice` where datetime>='$GetLastDateTime' and datetime<='$Now' group by DATE_FORMAT(datetime,'%Y-%m-%d %H:00:00') ORDER BY `datetime` DESC";

foreach($query as $id=>$Query) {
	mysqli_query($dbLink,$Query) or die(mysqli_error());
	echo "Executed Query #:".$id;
}
?>