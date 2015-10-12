<?php

require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Dj_dsp extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'dj_dsp';                  // table name
      
	  public $id;
	  public $dsp_portal_name;
	  public $status;
	 
  
	  
    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Dj_dsp',$k,$v); }


}


?>
