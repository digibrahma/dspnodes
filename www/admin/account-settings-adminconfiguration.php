<?php

/*
Revive Ad server 3.0.2 and This is custom file to configure admin details from Dreamajax Technologies
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/max/Admin_DA.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

 $table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
// Create a new option object for displaying the setting's page's HTML form


$oOptions = new OA_Admin_Option('settings');

$prefSection = "adminconfiguration";

$setPref = $oOptions->getSettingsPreferences($prefSection);

$title = $setPref[$prefSection]['name'];


// Display the settings page's header and sections

$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('account-settings-index', $oHeaderModel);


if (isset($_POST['submitok']) && $_POST['submitok'] == 'true')
{

  	
	               if(!empty($_POST['sdk_android'])) 
		       {
				$select2=OA_Dal_Delivery_query("select * from {$table_prefix}dj_admin_configuration");

				if(OA_Dal_Delivery_numRows($select2)>0)
				{
	   				OA_Dal_Delivery_query("update {$table_prefix}dj_admin_configuration set SDK_Androidpath ='".$_POST['sdk_android']."'");	
				}
				else
				{
					if($_POST['sdk_mobileandroid']==true){$is_enabled=1;}else{$is_enabled=0;}

					OA_Dal_Delivery_query("INSERT INTO {$table_prefix}dj_admin_configuration(SDK_Androidpath)VALUES('".$_POST['sdk_android']."'");
			
							 	 	 
			     	}
		
			}




					$select2=OA_Dal_Delivery_query("select * from {$table_prefix}dj_admin_configuration");

					if(OA_Dal_Delivery_numRows($select2)>0)
					{
						OA_Dal_Delivery_query("update {$table_prefix}dj_admin_configuration set SDK_iOSpath ='".$_POST['sdk_ios']."'");

						 	 	
					}
					else
					{
						OA_Dal_Delivery_query("INSERT INTO {$table_prefix}dj_admin_configuration(SDK_iOSpath)VALUES('".$_POST['sdk_ios']."')");
						 	 	 
					}
}	

// Set the correct section of the settings pages and display the drop-down menu


$select=OA_Dal_Delivery_query("select * from {$table_prefix}dj_admin_configuration");

$row=OA_Dal_Delivery_fetchAssoc($select);

$djax_mobileappandroid='';

$djax_mobileappios='';





$djax_mobileappandroid=array (
		'text'    => 'Mobile app android ',
		'items'   => array (
		   			 array (
		       			 	'type'    => 'text',
						'name'    => 'sdk_android',
						'text'    => 'Mobile android SDK',
						'size'    =>  50,
						'value'	  => $row['SDK_Androidpath']


		    				),
					    array (
				       			 'type'    => 'break'
	
			  		  )
                                        
			
	)
	);
	


$djax_mobileappios=array (
		'text'    => 'Mobile app IOS ',
		'items'   => array (
                                           array (
		       			 	'type'    => 'text',
						'name'    => 'sdk_ios',
						'text'    => 'Mobile  IOS',
						'size'    => 50,
						'value'	  => $row['SDK_iOSpath']
		    				),

					    array (
				       			 'type'    => 'break'
				  		  )	
	
	)
	);


$aSettings=array($djax_mobileappandroid,$djax_mobileappios);


$oOptions->show($aSettings, null);

phpAds_PageFooter();

?>
