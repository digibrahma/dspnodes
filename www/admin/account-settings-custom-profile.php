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
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';


// Required files

require_once MAX_PATH . '/lib/OA/Dll.php';

require_once MAX_PATH . '/lib/OX/Admin/UI/ViewHooks.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('mobile');
$prefSection = "custom-profile";


// Set the correct section of the settings pages and display the drop-down menu
$setPref = $oOptions->getSettingsPreferences($prefSection);
$title = $setPref[$prefSection]['name'];

// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('custom-profile-index', $oHeaderModel);
require_once MAX_PATH . '/lib/OA/Admin/Template.php';

$oTpl = new OA_Admin_Template('custom-profile.html');

$query = mysql_query("select * from oxm_profile order by profileid ASC") or die(mysql_error());
$profiles = array();
$i =1;
while($row = mysql_fetch_assoc($query))
{
	$profiles[$i]['name'] = $row['name'];
	$profiles[$i]['profileid'] = $row['profileid'];
	$profiles[$i]['type'] = 0;
	$i++;
}

 $aCount = array(
    'profiles'        => count($profiles)
);


$oTpl->assign('aProfiles', $profiles);
$oTpl->assign('aCount', $aCount);

OX_Admin_UI_ViewHooks::registerPageView($oTpl, 'account-settings-custom-profile');

$oTpl->display();
phpAds_PageFooter();

function buildHeaderModel()
{
    $builder = new OA_Admin_UI_Model_InventoryPageHeaderModelBuilder();
    return $builder->buildEntityHeader(array(), 'profiles', 'list');
}


?>
  
