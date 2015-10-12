<?php
header("content-type: application/json"); 


// Set value from loop 

$product = array();

$product[1] = array('ck' => 'http://www.google.com','lg'=>'http://localhost/Nbanner/lg.php','content'=>'<img src="http://localhost/Nbanner/images/1.jpg" />');
$product[2] = array('ck' => 'http://www.google.com','lg'=>'http://localhost/Nbanner/lg.php','content'=>'<div>sample</div>');
$product[3] = array('ck' => 'http://www.google.com','lg'=>'http://localhost/Nbanner/lg.php','content'=>'<script type="text/javascript" src="http://192.168.1.200/development/alam_dev/ads/www/delivery/banners.js?zoneid=326"></script>');

// Set value from loop



if (isset($_GET['id'])){

	$id = $_GET['id'];
	$array = array();
	$array['id'] = $id;
	$array['products'] = $product[$id];

} 
 
echo $_GET['callback']. '('. json_encode($array) . ')';    

?>
