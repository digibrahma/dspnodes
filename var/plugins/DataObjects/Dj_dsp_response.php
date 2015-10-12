<?php


require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Dj_dsp_response extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'dj_dsp_response';                      // table name
public $response_id;
public $datetime;
public $requset_id;
public $id;
public $imp_id;
public $imp_width;
public $imp_height;
public $seat;
public $floor_price;
public $advertiser_bid_price;  
public $smaato_bid_price;
public $admin_rev;
public $adid;
public $bannerid;
public $campaign_id;    
public $type;
public $win_notice;           
    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Dj_dsp_response',$k,$v); }

}


