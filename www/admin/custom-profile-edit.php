<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

// Require the initialisation file
require_once '../../init.php';

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

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('user');


// Prepare an array for storing error messages
$aErrormessage = array();
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {

	 $name 	= $_POST['profile'];
	 $profileid = $_POST['profileid'];
   // Queue confirmation message
  	  $translation = new OX_Translation ();

	$query = mysql_query("select * from oxm_profile") or die(mysql_error());

$values = array();
	while($row = mysql_fetch_array($query))
		{
			$validateValues = strtolower($row['name']);
			$values[$row['name']] = $validateValues;
			$validateValues = '';
		}
$validateName = strtolower($name);

	if(in_array($validateName,$values))
		{
		   // Queue confirmation message

 	$translated_message = $translation->translate ("<font color='#FF0000'>This <b>Profile Name</b> already added</font>");
   	 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
			
		}
		else {
			if(!empty($profileid))
			{
			mysql_query("update oxm_profile set name = '".$name."' where profileid=".$profileid) or die(mysql_error());

			 $translated_message = $translation->translate ("Profile {$name} has been Updated ");
		   	 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
		 	 OX_Admin_Redirect::redirect("custom-profile-edit.php?profileid=".$profileid);

			}
			else
			{

				mysql_query("insert into oxm_profile(name) values('".$name."')") or die(mysql_error());
	
				$profileid = '';
			 $translated_message = $translation->translate ("Profile {$name} has been Added ");
		   	 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
		 	 OX_Admin_Redirect::redirect("account-settings-custom-profile.php");

			}
		}

}




// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('custom-profile-index', $oHeaderModel);

if(isset($_GET['profileid']) && !empty($_GET['profileid']))
{

$profileid = $_GET['profileid'];

$query = mysql_query("select * from oxm_profile where profileid =".$profileid) or die(mysql_error());

		$row = mysql_fetch_assoc($query);
		$name = $row['name'];
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
                'name'    => 'profile',
                'value'   => $name ,
                'text'    => 'Profile Name',
                'size'    => 50

            ),
			array (
					'type'    => 'hidden',
					'name'  => 'profileid',
					'value'  => $profileid
		
				    ),
	 array (
                'type'    => 'break'
            ) 

        )
    )
);

$oOptions->show($aSettings, $aErrormessage);

// Display the page footer
phpAds_PageFooter();

?>
  
