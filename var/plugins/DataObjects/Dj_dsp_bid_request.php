<?php


require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Dj_dsp_bid_request extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'dj_dsp_bid_request';                      // table name
    public $id;
public $datetime;
public $exchange_id;
public $at;
public $device_connectiontype;
public $device_devicetype;
public $device_geo_country;
public $device_geo_latitude;
public $device_geo_longitude;
public $device_geo_type;
public $device_ip;
public $device_js;
public $device_make;
public $device_model;
public $device_os;
public $device_ua;
public $ext_udi;
public $bid_request_id;
public $imp_banner_type;
public $imp_banner_height;
public $imp_banner_mimes;
public $imp_banner_position;
public $imp_banner_width;
public $imp_bidfloor;
public $imp_displaymanager;
public $imp_id;
public $site_category;
public $site_domain;
public $site_id;
public $site_name;
public $publisher_id;
public $user_gender;
public $user_yob;


      
    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Dj_dsp_bid_request',$k,$v); }


}


