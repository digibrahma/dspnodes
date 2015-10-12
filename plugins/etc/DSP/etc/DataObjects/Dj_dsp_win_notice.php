<?php

require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Dj_dsp_win_notice extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'dj_dsp_win_notice';                  // table name
      
	  public $id;
	 public $datetime;
	  public $auctionID;
	  public $request_id;
	  public $bidid;
	  public $price;
	  public $currency;
	  public $impid;
	  public $seatid;
	  public $adid;
	
	  
    /* Static get */
   function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Dj_dsp_win_notice',$k,$v); }
 
}

?>

