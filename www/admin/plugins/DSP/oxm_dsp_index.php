<?php

require_once '../../../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';
require_once LIB_PATH . '/Plugin/Component.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);


$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];




if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {

	$translation = new OX_Translation();

	if(empty($_POST['dspname'])){


			$translated_message = $translation->translate ("<b style='color:red'>Required to fill all the fields.</b>");
			OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
	}else{

	
	$name=$_POST['dspname'];
	

			
			
			

			//	$time=date('Y-m-d H:i:s');

			

				OA_Dal_Delivery_query("INSERT INTO {$table_prefix}dj_dsp(
								
								dsp_portal_name,status)
							 VALUES(
								'".$name."','1'
								
								)")or die(mysql_error());
			
				$translated_message = $translation->translate ("<b>DSP Buyer</b> Account Has Been Set.");
				OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
				OX_Admin_Redirect::redirect("plugins/DSP/oxm_dsp_list.php");

		
	
	
	}

   

}


$aErrormessage = array();
$oOptions = new OA_Admin_Option('settings');
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel("DSP Portal Settings");
phpAds_PageHeader('dsp-add', $oHeaderModel);


		
//$currencytype=array('1'=>'EUR','2'=>'CUR');

//print_r($value);
$aSettings = array(
   array (
        'text'    => 'DSP Portal Settings',
        'items'   => array (
					array (
					'type'    => 'text',
					'text'    => 'DSP Portal Name',
					'name'    => 'dspname',
					'value'   => '',
					'req'     => true
				    )
				
			  )
	 )

);

//$oOptions->show($aSettings, $aErrormessage);

$oOptions->show($aSettings, null);

phpAds_PageFooter();

?>
