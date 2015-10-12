<?php
ob_start();
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
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';
//require_once MAX_PATH . '/lib/OA/Central/AdNetworks.php';
require_once MAX_PATH . '/lib/max/Admin_DA.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';

OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN,OA_ACCOUNT_MANAGER);

phpAds_PageHeader('redirection-settings-index', $oHeaderModel);

$conf=$GLOBALS['_MAX']['CONF'];

$oOptions = new OA_Admin_Option('user');

$aErrormessage = array();
$translation = new OX_Translation ();

$msg='';
if (isset($_POST['submit'])) 
{

	$getselect=OA_Dal_Delivery_query("update ".$conf['table']['prefix'].$conf['table']['affiliates']." SET redirecturl='".$_POST['redirecturl']."' where affiliateid='".$_GET['affiliateid']."'");

    $translation = new OX_Translation ();
   
     $translated_message = "Redirect url is updated";
     OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

	header("location:account-settings-redirect-setup.php");
		
}

// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
?>

<style type="text/css"/>
#clientid
{
display:none;
}
</style>
<?php


if(!empty($_GET['affiliateid']))
{

		$tempQuery=OA_Dal_Delivery_query("select * from ".$conf['table']['prefix'].$conf['table']['affiliates']." where affiliateid='".$_GET['affiliateid']."'");

$aRows=OA_Dal_Delivery_fetchAssoc($tempQuery);


echo "<form action='' method='post'><input type='hidden' name='id' value='".$aRows['name']."'><table width='100%' border='0' cellspacing='0' cellpadding='0'>";

echo "<tr width='100%'>";

echo "<td width='10%'>Publisher Name:</td><td width='50%'>".$aRows['name']."</td></tr>";

echo "<tr width='100%'>";

echo "<td width='10%'>Redirect Url:</td><td width='50%'><input type='text' name='redirecturl' id='name' value='".$aRows['redirecturl']."'></td></tr>";

echo "<br><br><br>";


echo "<tr width='100%'><td width='10%'></td><td width='50%'><input type='submit' name='submit' value='Save' id='save'> </td></tr>";


echo "</table></form>";
}

phpAds_PageFooter();
ob_flush();
?>
	
