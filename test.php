<?php
require_once 'init.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';


//mysql_connect("digibrahmadsp.cluster-ci0lgixaghcf.us-east-1.rds.amazonaws.com","digibrahma_dsp","digibrahma~17");
//mysql_select_db("digibrahma_dsp");



$dbh = mysql_query("select * from sama_livemis limit 1") or (error_de());


function error_de() {
header("HTTP/1.0 404 Not Found");
}		
?>