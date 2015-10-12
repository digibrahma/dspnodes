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
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

require_once LIB_PATH . '/Plugin/Component.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:"."Error1--->".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

// Create a new option object for displaying the setting's page's HTML form


// Prepare an array for storing error messages
if (isset($_POST['submit']))
{
	
	$_terawurfl_path = $_POST['terawurfl_path'];

if(!empty($_terawurfl_path)) {

		$query = mysql_query("select * from oxm_terawurfl") or die("Error1--->".mysql_error());

	  	$translation = new OX_Translation ();

		if(mysql_num_rows($query) > 0)
		{

			mysql_query("update oxm_terawurfl set terawurfl_path = '".$_terawurfl_path."' ") or die("Error2--->".mysql_error());

		}
		else
		{
			mysql_query("insert into oxm_terawurfl(terawurfl_path) values('".$_terawurfl_path."')") or die("Error3--->".mysql_error());
		}

		   // Queue confirmation message

		 	$translated_message = $translation->translate ("<b>Tera-WURFL</b> Path Has Been Set.");
		   	 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

	} else {

			$translated_message = $translation->translate ("<font color='red'><b>Tera-WURFL</b> Path is required.</font>");
		   	 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

	}

}



phpAds_PageHeader('terawurfl-settings-index', $oHeaderModel);

$query = mysql_query("select * from oxm_terawurfl") or die("Error4--->".mysql_error());


$_path = '';

	if(mysql_num_rows($query)) {

		$row = mysql_fetch_assoc($query);

		$_path = $row['terawurfl_path'];

	} 


echo "
    <table width='100%'>
        <tbody><tr>
            <td colspan='4' height='25'>

                           
                              <img src='assets/images/icon-settings.gif' height='16' width='16' align='absmiddle'>&nbsp;
                <b>Tera-WURFL Path Settings</b>
            </td>
        </tr>
        <tr height='1'>
	        <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
	       <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

	  	 <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='1'></td>
	  	  <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
        </tr>
        <tr>
            <td colspan='4' height='10'><img src='assets/images/spacer.gif' height='1' width='30'></td>
        </tr>
</table>";

echo "<form name=form1 id=form1 action='account-settings-tera-wurfl.php' method='post' >
<table width='60%'>
<tr>
    <td>&nbsp;</td>
    <td>&nbsp;Tera-WURFL Path </td>
    <td>&nbsp;</td>
    <td>&nbsp; </td>
    <td>&nbsp;<input type='text' name='terawurfl_path' size='75' value={$_path} ></td>
  </tr>

        <tr>
            <td colspan='7' height='10'><img src='assets/images/spacer.gif' height='1' width='30'></td>
        </tr>
        <tr>
            <td colspan='7' height='10'><img src='assets/images/spacer.gif' height='1' width='30'></td>
        </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>    
    <td>&nbsp;&nbsp;Ex: <b>http://www.yourdomain.com/Tera-WURFL</b></td>
<td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

<br/><br/>

</table> ";

echo "
    <table width='100%'>
        <tbody>
        <tr height='1'>
	        <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
	       <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

	  	 <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='1'></td>
	  	  <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
        </tr>
        <tr>
            <td colspan='4' height='10'><img src='assets/images/spacer.gif' height='1' width='30'></td>

        </tr>
<tr>
<td><input type='submit' name='submit' value='Save Changes' /></td>
</tr>



</table>";

echo "</form>";

// Display the page footer
phpAds_PageFooter();

?>
  
