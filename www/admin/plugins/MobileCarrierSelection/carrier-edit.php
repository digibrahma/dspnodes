<link rel="stylesheet" type="text/css" href="css/upload-media.css"/>

<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| ==========                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: account-settings-database.php 34688 2009-04-01 16:18:28Z andrew.hill $
*/

// Require the initialisation file
require_once '../../../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/max/Admin_DA.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];



// Display the settings page's header and sections
//$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);

$oOptions = new OA_Admin_Option('user');



$aErrormessage = array();
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {


	 $id = !empty($_POST['id']) ?$_POST['id'] : '';
	$carriername = !empty($_POST['carriername']) ?$_POST['carriername'] : ''; 
	$country = !empty($_POST['country']) ?$_POST['country'] : ''; 

	$start_ip = !empty($_POST['start_ip']) ?$_POST['start_ip'] : ''; 
	$end_ip =  !empty($_POST['end_ip']) ?$_POST['end_ip'] : ''; 


   // Queue confirmation message
  	  $translation = new OX_Translation ();



		          if(!filter_var($start_ip, FILTER_VALIDATE_IP))
			  {
			  	$translated_message = $translation->translate ("Please Enter valid IP format");
		   		 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

		 		OX_Admin_Redirect::redirect("plugins/MobileCarrierSelection/carrier-edit.php?id=".$id);
			  }
			  else if(!filter_var($end_ip, FILTER_VALIDATE_IP))
			  {
			    	$translated_message = $translation->translate ("Please Enter valid IP format");
		   		 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

		 		OX_Admin_Redirect::redirect("plugins/MobileCarrierSelection/carrier-edit.php?id=".$id);
			  }
				
			if(!empty($id) && !empty($carriername) && !empty($country) && !empty($start_ip) && !empty($end_ip))
			{
			
				 mysql_query("update ".$table_prefix."carrier_detail set carriername = '".$carriername."',  country = '".$country."', start_ip = '".$start_ip."', end_ip = '".$end_ip."' where id =".$id) or die(mysql_error());

				 $translated_message = $translation->translate ("Teleco Carrier <b>{$name}</b> has been Updated ");
		   		 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

		 		OX_Admin_Redirect::redirect("plugins/MobileCarrierSelection/carrier-edit.php?id=".$id);

			}
			else
			{

				 $translated_message = $translation->translate ("<font color='red'> All Field Values are required.");
		   		 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
		 	 	OX_Admin_Redirect::redirect("plugins/MobileCarrierSelection/carrier-edit.php?id=".$id);


			}

}


//$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader("carrierdetail", $oHeaderModel);

if(isset($_GET['id']) && !empty($_GET['id']))
{

$carrierId = $_GET['id'];

$query = mysql_query("select * from ".$table_prefix."carrier_detail where id =".$carrierId) or die(mysql_error());
	$row = mysql_fetch_assoc($query);

	$id = $row['id'];
	$carriername = $row['carriername'];
	$country = $row['country'];
	$start_ip = $row['start_ip'];
	$end_ip = $row['end_ip'];
}

$aSettings = array (
    array (
        'text'  => "Add New Custom Client Profile",
        'items' => array (    

        array (
                'type'    => 'break'
            ),
          array (
					'type'    => 'text',
					'name'  => 'id',
					'value'  => $id
				
		
				    ),

            array (
                'type'    => 'text',
                'name'    => 'carriername',
                'value'   => $carriername ,
                'text'    => 'Carrier Name',
                'size'    => 50

            ),
			
	 array (
                'type'    => 'break'
            ) ,
            array (
                'type'    => 'text',
                'name'    => 'country',
                'value'   => $country ,
                'text'    => 'Country',
                'size'    => 50

            ),
			
	 array (
                'type'    => 'break'
            ) ,
            array (
                'type'    => 'text',
                'name'    => 'start_ip',
                'value'   => $start_ip ,
                'text'    => 'Start IP Address',
                'size'    => 50

            ),
			
	 array (
                'type'    => 'break'
            ) ,
            array (
                'type'    => 'text',
                'name'    => 'end_ip',
                'value'   => $end_ip ,
                'text'    => 'End IP Address',
                'size'    => 50

            )

        )
    )
);

$oOptions->show($aSettings, $aErrormessage);

phpAds_PageFooter();

?>
  

