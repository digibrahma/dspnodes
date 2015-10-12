<?php
require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Excel extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'excel';                      // table name
    public $StatusText;                    
    public $start_ip;                
    public $end_ip;                          
    public $country;      
    public $carriername;      
      
    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Excel',$k,$v); }


    var $defaultValues = array(
                'start_ip' => '',
                'end_ip' =>'',
                'country' => '',                
                'carriername' => '',                
                                
                              
                );
 
}
