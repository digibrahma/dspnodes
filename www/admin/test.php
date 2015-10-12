<?php

require_once '../../init.php';

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);

$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$vs=mysql_query("SELECT * FROM rv_dj_dsp_response as res join rv_dj_dsp_bid_request AS req ON req.id!=res.requset_id and res.datetime>='2015-09-01'") or die(mysql_error());

while($v=mysql_fetch_array($vs))
{
print_r($v);
}
/*

mysql_query("CREATE TABLE IF NOT EXISTS `rv_dj_axonix_win_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `request_id` int(11) NOT NULL,
  `auctionID` varchar(200) NOT NULL,
  `bidid` varchar(200) NOT NULL,
  `price` double(10,2) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `impid` varchar(200) NOT NULL,
  `seatid` varchar(200) NOT NULL,
  `adid` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
)");


mysql_query("CREATE TABLE IF NOT EXISTS `rv_dj_axonix_response` (
  `response_id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `requset_id` mediumint(9) NOT NULL DEFAULT '0',
  `id` varchar(255) DEFAULT NULL,
  `imp_id` int(11) DEFAULT NULL,
  `imp_width` int(11) DEFAULT NULL,
  `imp_height` int(11) DEFAULT NULL,
  `seat` varchar(255) DEFAULT NULL,
  `floor_price` float(11,4) DEFAULT NULL,
  `advertiser_bid_price` float(11,4) DEFAULT NULL,
  `smaato_bid_price` float(11,4) DEFAULT NULL,
  `admin_rev` decimal(10,2) NOT NULL,
  `adid` mediumint(9) NOT NULL DEFAULT '0',
  `bannerid` mediumint(9) NOT NULL DEFAULT '0',
  `campaign_id` mediumint(9) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '',
  `win_notice` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`response_id`)
)");

mysql_query("CREATE TABLE IF NOT EXISTS `rv_dj_axonix_bid_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `exchange_id` int(11) NOT NULL,
  `at` int(11) NOT NULL,
  `device_connectiontype` int(11) NOT NULL,
  `device_devicetype` int(11) NOT NULL,
  `device_geo_country` varchar(200) NOT NULL,
  `device_geo_latitude` varchar(200) NOT NULL,
  `device_geo_longitude` varchar(200) NOT NULL,
  `device_geo_type` int(11) NOT NULL,
  `device_ip` varchar(200) NOT NULL,
  `device_js` varchar(200) NOT NULL,
  `device_make` varchar(200) NOT NULL,
  `device_model` varchar(200) NOT NULL,
  `device_os` varchar(200) NOT NULL,
  `device_ua` varchar(300) NOT NULL,
  `ext_udi` int(11) NOT NULL,
  `bid_request_id` varchar(255) NOT NULL,
  `imp_banner_type` varchar(200) NOT NULL,
  `imp_banner_height` int(11) NOT NULL,
  `imp_banner_mimes` varchar(200) NOT NULL,
  `imp_banner_position` int(11) NOT NULL,
  `imp_banner_width` int(11) NOT NULL,
  `imp_bidfloor` decimal(10,4) NOT NULL,
  `imp_displaymanager` varchar(200) NOT NULL,
  `imp_id` int(11) NOT NULL,
  `site_category` varchar(200) NOT NULL,
  `site_domain` varchar(200) NOT NULL,
  `site_id` int(11) NOT NULL,
  `site_name` varchar(200) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `user_gender` varchar(50) NOT NULL,
  `user_yob` int(4) NOT NULL,
  PRIMARY KEY (`id`)
)");

//echo "select * from ".$table_prefix."users where user_id=1";

//mysql_query("UPDATE `rv_users` SET `password`='21232f297a57a5a743894a0e4a801fc3' WHERE `user_id`=1");


//$user=mysql_fetch_array(mysql_query("select * from ".$table_prefix."users where user_id=1"));
/*
$value=mysql_query("SELECT
			d.bannerid AS ad_id,
			d.campaignid AS placement_id,
			d.status AS status,
			d.description AS name,
			d.storagetype AS type,
			d.filename AS filename,
			d.imageurl AS imageurl,
			d.width AS width,
			d.height AS height,
			d.weight AS weight,
			d.url AS url,
			d.bannertext AS bannertext,
			d.compiledlimitation AS compiledlimitation,
			d.alt_filename AS alt_filename,
			d.alt_imageurl AS alt_imageurl,
			c.campaignid AS campaign_id,
			c.revenue as revenue,
			m.clientid AS client_id,
			c.weight AS campaign_weight,
			m.agencyid AS agency_id
		    FROM
			rv_banners AS d,
			rv_campaigns AS c,
			rv_clients AS m
		    WHERE
			
			c.revenue_type=1 
			AND
			d.campaignid = c.campaignid
			AND
			m.clientid = c.clientid
			AND
			c.status=0
			AND 
			d.status=0
			AND 
			d.width='168'
			AND 
			d.height='28'
			 AND 
			d.storagetype='web' 
			 AND
			masterbanner!='-1' 
			AND (case when c.dsp_portals!='' then 1 IN(c.dsp_portals) ELSE 1 NOT IN(c.dsp_portals) end)");
			 
	while($result=mysql_fetch_array($value))
	{	 
print_r($result);			 

}
*/
//print_r($user);
/*
 
 Array
(
    [0] => 1
    [user_id] => 1
    [1] => Administrator
    [contact_name] => Administrator
    [2] => info@digibrahma.in
    [email_address] => info@digibrahma.in
    [3] => admin
    [username] => admin
    [4] => 469b8544afd2066747970941769035e6
    [password] => 469b8544afd2066747970941769035e6
    [5] => en
    [language] => en
    [6] => 2
    [default_account_id] => 2
    [7] => 
    [comments] => 
    [8] => 1
    [active] => 1
    [9] => 
    [sso_user_id] => 
    [10] => 2015-04-19 04:10:05
    [date_created] => 2015-04-19 04:10:05
    [11] => 2015-06-09 17:47:38
    [date_last_login] => 2015-06-09 17:47:38
    [12] => 2015-04-30 01:26:18
    [email_updated] => 2015-04-30 01:26:18
)

*/

?>
