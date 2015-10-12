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
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


// Prepare an array for storing error messages
if (isset($_POST['submit']))
{
	$user_id = 1;

		$zonewidth1 	= $_POST['zonewidth1'];
		$zoneheight1 	= $_POST['zoneheight1'];
		$zonewidth2 	= $_POST['zonewidth2'];
		$zoneheight2 	= $_POST['zoneheight2'];
		$zonewidth3 	= $_POST['zonewidth3'];
		$zoneheight3 	= $_POST['zoneheight3'];
		$zonewidth4 	= $_POST['zonewidth4'];
		$zoneheight4 	= $_POST['zoneheight4'];

   // Queue confirmation message
  	  $translation = new OX_Translation ();

if(!empty($zonewidth1) && !empty($zoneheight1) &&  !empty($zonewidth2) &&  !empty($zoneheight2) &&  !empty($zonewidth3) &&  !empty($zoneheight3) &&  !empty($zonewidth4) &&  !empty($zoneheight4)) {


	$query = mysql_query("select * from oxm_mobilezonesize where user_id=".$user_id) or die(mysql_error());

		if(mysql_num_rows($query) > 0)
		{


				mysql_query("update oxm_mobilezonesize set width = ".$zonewidth1.",height = ".$zoneheight1."  where user_id=".$user_id." and zonecategory = 1 ") or die(mysql_error());

			mysql_query("update oxm_mobilezonesize set width = ".$zonewidth2.",height = ".$zoneheight2."  where user_id=".$user_id." and zonecategory = 2 ") or die(mysql_error());

			mysql_query("update oxm_mobilezonesize set width = ".$zonewidth3.",height = ".$zoneheight3."  where user_id=".$user_id." and zonecategory = 3 ") or die(mysql_error());

			mysql_query("update oxm_mobilezonesize set width = ".$zonewidth4.",height = ".$zoneheight4."  where user_id=".$user_id." and zonecategory = 4 ") or die(mysql_error());

			$translated_message = $translation->translate ("<b>Mobile Zone Size</b> Has Been Updated"); 

		}
		else
		{

		mysql_query("insert into oxm_mobilezonesize(user_id,zonecategory,width,height) values(".$user_id.", 1, ".$zonewidth1.",".$zoneheight1.")") or die(mysql_error());
		mysql_query("insert into oxm_mobilezonesize(user_id,zonecategory,width,height) values(".$user_id.", 2, ".$zonewidth2.",".$zoneheight2.")") or die(mysql_error());
		mysql_query("insert into oxm_mobilezonesize(user_id,zonecategory,width,height) values(".$user_id.", 3, ".$zonewidth3.",".$zoneheight3.")") or die(mysql_error());
		mysql_query("insert into oxm_mobilezonesize(user_id,zonecategory,width,height) values(".$user_id.", 4, ".$zonewidth4.",".$zoneheight4.")") or die(mysql_error());


 			$translated_message = $translation->translate ("<b>Mobile Zone Size</b> Has Been Inserted"); 

		}

} else {


	$translated_message = $translation->translate ("<font color='red'><b>Mobile Zone Size</b> required.</font>"); 

}


   	 OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

}





phpAds_PageHeader('zonesize-settings-index', $oHeaderModel);

$width = array();
$height = array();

$user_id = OA_Permission::getUserId();

$query = mysql_query("select * from oxm_mobilezonesize where user_id=".$user_id) or die(mysql_error());
$i=1;
	while($row = mysql_fetch_assoc($query))
	{
		$width[$row['zonecategory']] = $row['width'];
		$height[$row['zonecategory']] = $row['height'];
	}
echo "
    <table width='100%'>
        <tbody><tr>
            <td colspan='4' height='25'>

                           
                              <img src='assets/images/icon-settings.gif' height='16' width='16' align='absmiddle'>&nbsp;
                <b>Mobile Zone Size Settings</b>
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

echo "<form name=form1 id=form1 action='account-settings-mobile-zone.php' method='post' >
<table width='60%'>
<tr>
    <td>&nbsp;</td>
    <td>&nbsp;Category 1 Mobile Zone Size</td>
    <td>&nbsp;</td>
    <td>[ width ]</td>
    <td>&nbsp;<input type='text' name='zonewidth1' size='10' value={$width[1]} ></td>
    <td>[ height ] </td>
    <td>&nbsp;<input type='text' name='zoneheight1' size='10' value={$height[1]} ></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;Category 2 Mobile Zone Size</td>
    <td>&nbsp;</td>
    <td>[ width ]</td>
    <td>&nbsp;<input type='text' name='zonewidth2' size='10' value={$width[2]}  ></td>
    <td>[ height ] </td>
    <td>&nbsp;<input type='text' name='zoneheight2' size='10' value={$height[2]}  ></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;Category 3 Mobile Zone Size</td>
    <td>&nbsp;</td>
    <td>[ width ]</td>
    <td>&nbsp;<input type='text' name='zonewidth3'  size='10' value={$width[3]} ></td>
    <td>[ height ] </td>
    <td>&nbsp;<input type='text' name='zoneheight3' size='10' value={$height[3]} ></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;Category 4 Mobile Zone Size</td>
    <td>&nbsp;</td>
    <td>[ width ]</td>
    <td>&nbsp;<input type='text' name='zonewidth4' size='10' value={$width[4]} ></td>
    <td>[ height ] </td>
    <td>&nbsp;<input type='text' name='zoneheight4'  size='10' value={$height[4]} ></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>


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
  
