<?php

require_once '../../../../init.php';

require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';


 $table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

/*
$title="Editing Adexchanges";

$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('ssp-list', $oHeaderModel);
*/
$aErrormessage = array();
$a='http://'.$GLOBALS['conf']['webpath']['admin'].'/';
$translation = new OX_Translation();


$adx_ids=explode(',',$_GET['aid']);


if($_GET['action']=='disable'){
	
	
	
	foreach($adx_ids as $id){		
		
		OA_Dal_Delivery_query("UPDATE {$table_prefix}dj_dsp SET status='0' WHERE id='".$id."' ")or die(mysql_error());
		OA_Dal_Delivery_query("update  {$table_prefix}campaigns set adx_id='' where adx_id=$id");
	}
	
	
	$translated_message = $translation->translate ("<b style='color:red'>Selected DSP has beed disabled Successfully.</b>");
	OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
	OX_Admin_Redirect::redirect("plugins/DSP/oxm_dsp_list.php");
}

if($_GET['action']=='enable'){
	
	foreach($adx_ids as $id){		
		
		OA_Dal_Delivery_query("UPDATE {$table_prefix}dj_dsp SET status='1' WHERE id='".$id."' ")or die(mysql_error());
	}
	
	
	$translated_message = $translation->translate ("<b>Selected DSP has beed Enabled Successfully.</b>");
	OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
	OX_Admin_Redirect::redirect("plugins/DSP/oxm_dsp_list.php");
}

if($_GET['action']=='delete'){	
	
	
	foreach($adx_ids as $id){		
		
		OA_Dal_Delivery_query("DELETE FROM {$table_prefix}dj_dsp WHERE id='".$id."' ")or die(mysql_error());
	}

	$translated_message = $translation->translate ("<b style='color:red'>Selected DSP has beed Deleted Successfully.</b>");
	OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
	
	OX_Admin_Redirect::redirect("plugins/DSP/oxm_dsp_list.php");
	
}




phpAds_PageFooter();
?>
