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

session_start();
// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);


$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];



if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {

	$translation = new OX_Translation();
	
	
	$aid=$_SESSION['aid'];
	
	if(empty($_POST['dspname'])){


			$translated_message = $translation->translate ("<b style='color:red'>Required to fill all the fields.</b>");
			OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
	}



		OA_Dal_Delivery_query("UPDATE {$table_prefix}dj_dsp SET
								dsp_portal_name='".$_POST['dspname']."'													WHERE id='".$_POST['aid']."'")or die(mysql_error());
			
				$translated_message = $translation->translate ("<b>DSP </b> Account Has Been Updated.");
				OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
				OX_Admin_Redirect::redirect("plugins/DSP/oxm_dsp_list.php");

		
	
	

  // OX_Admin_Redirect::redirect("oxm_ssp_list.php");

}




$aErrormessage = array();
$oOptions = new OA_Admin_Option('settings');
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel("DSP Editing");
phpAds_PageHeader('dsp-list', $oHeaderModel);

if(!empty($_GET['aid'])){


		$adx_Query=OA_Dal_Delivery_query("SELECT * FROM {$table_prefix}dj_dsp WHERE id='".$_GET['aid']."'")or die(mysql_error());

		$adx_Row=mysql_fetch_assoc($adx_Query);


	
		$aSettings = array(
		   array (
				'text'    => 'DSP Settings',
				'items'   => array (
							 array (
								'type'    => 'text',
								'text'    => 'DSP Portal Name',
								'name'    => 'dspname',
								'value'   => $adx_Row['dsp_portal_name'],
								'req'     => true
							),
array (
								'type'    => 'hiddenfield',
								'text'    => 'aid',
								'name'    => 'aid',
								'value'   => $adx_Row['id'],
							)
							
							
					  )
			 )

		);

		$oOptions->show($aSettings, null);

}else{
		
	$translation = new OX_Translation();
	OX_Admin_Redirect::redirect("plugins/DSP/oxm_dsp_list.php");
}

phpAds_PageFooter();

?>
