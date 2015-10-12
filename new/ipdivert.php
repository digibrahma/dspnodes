<?php

include "db.php";
$user_ip = get_ip();
//echo $user_ip;


$query_to_find_ip = mysqli_query($connxn,"select lower(name) as name from djax_iprange where INET_ATON(hostmin)<=INET_ATON('".$user_ip."') AND INET_ATON(hostmax)>=INET_ATON('".$user_ip."') limit 1") or die(mysqli_error($connxn));
$count_find_ip = mysqli_num_rows($query_to_find_ip);
if($count_find_ip == 0)
{
    // No IP Found In Table, Proceed to Default Campaign
    $FoundCarrier = false;
    
} else{
    $result_find_ip = mysqli_fetch_array($query_to_find_ip);

    $FoundCarrier = $result_find_ip['name'];
    
}

//echo $FoundCarrier;exit;


function get_ip() {

		//Just get the headers if we can or else use the SERVER global
		if ( function_exists( 'apache_request_headers' ) ) {

			$headers = apache_request_headers();

		} else {

			$headers = $_SERVER;

		}

		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

			$the_ip = $headers['X-Forwarded-For'];

		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
		) {

			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];

		} else {
			
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

		}

		return $the_ip;

	}

?>