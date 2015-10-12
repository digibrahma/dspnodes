<?php
//require_once '../init.php';
//require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
//error_reporting(E_ALL);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
mysql_connect("digibrahmadsp.cluster-ci0lgixaghcf.us-east-1.rds.amazonaws.com","digibrahma_dsp","digibrahma~17");
mysql_select_db("digibrahma_dsp");


echo "bannerid, win_notice<br>";
$dbh = mysql_query("select adid,count(*) as cnt from rv_dj_dsp_win_notice where datetime>='2015-10-10 10:00:00' group by adid") or die(mysql_errno());

while($result=mysql_fetch_array($dbh)) {
    
    echo $result['adid']."-".number_format($result['cnt'],0)."<br>";
    
}


?>