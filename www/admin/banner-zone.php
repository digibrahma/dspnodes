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
require_once MAX_PATH . '/www/admin/lib-maintenance-priority.inc.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH . '/www/admin/lib-size.inc.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/max/other/stats.php';
require_once MAX_PATH . '/lib/max/Admin_DA.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

// Security check
////////openx mods///////
////////// add OA_ACCOUNT_ADVERTISER
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER);
OA_Permission::enforceAccessToObject('clients',   $clientid);
OA_Permission::enforceAccessToObject('campaigns', $campaignid);
OA_Permission::enforceAccessToObject('banners',   $bannerid);
$_REQUEST['bannerid']= $bannerid;
/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid'] = $clientid;
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid] = $campaignid;
phpAds_SessionDataStore();


    // Get input parameters
    $advertiserId   = MAX_getValue('clientid');
    $campaignId     = MAX_getValue('campaignid');
    $bannerId       = MAX_getValue('bannerid');
    $aCurrentZones  = MAX_getValue('includezone');
    $listorder      = MAX_getStoredValue('listorder', 'name');
    $orderdirection = MAX_getStoredValue('orderdirection', 'up');
    $submit         = MAX_getValue('submit');

    // Initialise some parameters
    $pageName = basename($_SERVER['SCRIPT_NAME']);
    $tabindex = 1;
    $agencyId = OA_Permission::getAgencyId();
    $aEntities = array('clientid' => $advertiserId, 'campaignid' => $campaignId, 'bannerid' => $bannerId);

    // Process submitted form
    if (isset($submit))
    {
        $dalZones       = OA_Dal::factoryDAL('zones');
        $prioritise     = false;
        $error          = false;
        $aPreviousZones = Admin_DA::getAdZones(array('ad_id' => $bannerId));
        $aDeleteZones   = array();

        // First, remove any zones that should be deleted.
        if (!empty($aPreviousZones)) {
            $unlinked = 0;
            foreach ($aPreviousZones as $aAdZone) {
                $zoneId = $aAdZone['zone_id'];
                if ((empty($aCurrentZones[$zoneId])) && ($zoneId > 0))  {
                    // Schedule for deletion
                    $aDeleteZones[] = $zoneId;
                } else {
                    // Remove this key, because it is already there and does not need to be added again.
                    unset($aCurrentZones[$zoneId]);
                }
            }
        }

        // Unlink zones
        if (count($aDeleteZones)) {
            $unlinked = $dalZones->unlinkZonesFromBanner($aDeleteZones, $bannerId);
            if ($unlinked > 0) {
                $prioritise = true;
            } elseif ($unlinked == -1) {
                $error = true;
            }
 //======================================================Modified By DAC016============================================//
$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

              while (list($key, $value) = each($aDeleteZones)) {
              $del=mysql_query("select * from rv_zones where masterzone = -2 and zoneid=".$value)or die(mysql_error());
                  if(mysql_num_rows($del)>0){
    				   //die('in');
		             	     $j=$value;
	               	             $k=$bannerid;
	               	               	 for($i=0;$i<=3;$i++){	               	               
	               	               	 $j++;
	               	               	 $k++;	               	               	 
				 mysql_query("DELETE FROM  ".$table_prefix."ad_zone_assoc where zone_id=$j AND ad_id= $k") or die('error in');	               	               	                 }
				   
				        
              
                  }
              }
                    
 //======================================================Modified By DAC016============================================//                  
           
        }

        // Link zones
        if (count($aCurrentZones)) {
            $linked = $dalZones->linkZonesToBanner(array_keys($aCurrentZones), $bannerId);
            if (PEAR::isError($linked)
                || $linked == -1) {
                $error = $linked;
            } elseif($linked > 0) {
                $prioritise = true;
            }
//======================================================Modified By DAC016============================================//
$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

              while (list($key, $value) = each($aCurrentZones)) {
              $del=mysql_query("select * from rv_zones where masterzone = -2 and zoneid=".$key)or die(mysql_error());

                  if(mysql_num_rows($del)>0){

		             	     $j=$key;
	               	             $k=$bannerid;
	               	               	 for($i=0;$i<=3;$i++){	               	               
	               	               	 $j++;
	               	               	 $k++;	               	               	 
	  mysql_query("INSERT INTO ".$table_prefix."ad_zone_assoc (zone_id,ad_id,priority,link_type,priority_factor) VALUES ('".$j."','".$k."','0','1','1')") or die('error in');			
	               	               	 }
				   
				        
              
                  }
              }
           
//======================================================Modified By DAC016============================================//        
       }

        if ($prioritise) {
            // Run the Maintenance Priority Engine process
            OA_Maintenance_Priority::scheduleRun();
        }

        // Move on to the next page
        if (!$error) {
            // Queue confirmation message
            $translation = new OX_Translation ();
            if ($linked > 0) {
                $linked_message = $translation->translate ( $GLOBALS['strXZonesLinked'], array($linked));
            }
            if ($unlinked > 0) {
                $unlinked_message = $translation->translate ( $GLOBALS['strXZonesUnlinked'], array($unlinked));
            }
            if ($linked > 0 || $unlinked > 0) {
                $translated_message = $linked_message. ($linked_message != '' && $unlinked_message != '' ? ', ' : ' ').$unlinked_message;
                OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
          	}

            Header("Location: banner-zone.php?clientid={$clientid}&campaignid={$campaignid}&bannerid={$bannerid}");
            exit;
        }
    }

    // Display navigation
    $aOtherCampaigns = Admin_DA::getPlacements(array('agency_id' => $agencyId));
    $aOtherBanners = Admin_DA::getAds(array('placement_id' => $campaignId), false);
    MAX_displayNavigationBanner($pageName, $aOtherCampaigns, $aOtherBanners, $aEntities);

    // Main code
    $aAd = Admin_DA::getAd($bannerId);
    $aParams = array('agency_id' => $agencyId);
    if ($aAd['type'] == 'txt') {
        $aParams['zone_type'] = phpAds_ZoneText;
    } else {
        $aParams['zone_width'] = $aAd['width'] . ',-1';
        $aParams['zone_height'] = $aAd['height'] . ',-1';
    }
    $aPublishers = Admin_DA::getPublishers($aParams, true);

/* openxmods - DAC009 */

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


	$_banner_query = mysql_query("select * from ".$table_prefix."banners where masterbanner = '-2' and bannerid = ".$bannerId."");


	    if(mysql_num_rows($_banner_query)>0){


			foreach($aPublishers as $key => $_publishers) {


		$_query = mysql_query("select * from ".$table_prefix."affiliates as a, ".$table_prefix."zones as z where z.affiliateid = a.affiliateid and  z.masterzone = '-2' and a.affiliateid = ".$key." limit 0, 1");

			     if(mysql_num_rows($_query) == 0) {

				unset($aPublishers[$key]); 

			    }



			}
	   } else {

   	       		$_banner_query = mysql_query("select * from ".$table_prefix."banners where bannerid = ".$bannerId."")or die("Error-->in else 8".mysql_error());

		$value=mysql_fetch_array($_banner_query);
 	    
	   // if(mysql_num_rows($_banner_query)>0){

		if($value['masterbanner'] == '-3') {


		     foreach($aPublishers as $key => $_publishers) {


		$_query = mysql_query("select * from ".$table_prefix."affiliates as a, ".$table_prefix."zones as z where z.affiliateid = a.affiliateid and  z.masterzone = '-3' and a.affiliateid = ".$key." limit 0, 1");

			     if(mysql_num_rows($_query) == 0) {

				unset($aPublishers[$key]); 

			    }

		    }
		}
		else if($value['masterbanner'] == '-4')
              {   //mobile html banner






		     foreach($aPublishers as $key => $_publishers) {
	

		$_query = mysql_query("select * from ".$table_prefix."affiliates as a, ".$table_prefix."zones as z where z.affiliateid = a.affiliateid and  z.masterzone = '-4' and a.affiliateid = ".$key." limit 0, 1");

//echo "select * from ".$table_prefix."affiliates as a, ".$table_prefix."zones as z where z.affiliateid = a.affiliateid and  z.masterzone = '-4' and a.affiliateid = ".$key." limit 0, 1";


			     if(mysql_num_rows($_query) == 0) {

				unset($aPublishers[$key]); 

			    }

		    }



                }


else {
	
		     foreach($aPublishers as $key => $_publishers) {


		$_query = mysql_query("select * from ".$table_prefix."affiliates as a, ".$table_prefix."zones as z where z.affiliateid = a.affiliateid and  z.masterzone = '-1' and a.affiliateid = ".$key." limit 0, 1");

			     if(mysql_num_rows($_query) == 0) {

				unset($aPublishers[$key]); 

			    }

		    }		
		
		}

	   }



/* openxmods - DAC009 */


    $aLinkedZones = Admin_DA::getAdZones(array('ad_id' => $bannerId), false, 'zone_id');

    echo "
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<form name='zones' action='$pageName' method='post'>
<input type='hidden' name='clientid' value='$advertiserId'>
<input type='hidden' name='campaignid' value='$campaignId'>
<input type='hidden' name='bannerid' value='$bannerId'>";

    MAX_displayZoneHeader($pageName, $listorder, $orderdirection, $aEntities);

    if ($error) {
        $errorMoreInformation = '';
        if (PEAR::isError($error)) {
            $errorMoreInformation = $error->getMessage();
        }
        // Message
        echo "<br>";
        echo "<div class='errormessage'><img class='errormessage' src='" . OX::assetPath() . "/images/errormessage.gif' align='absmiddle'>";
        echo "<span class='tab-r'>{$GLOBALS['strUnableToLinkBanner']} {$errorMoreInformation}</span>";
        echo "</div>";
    } else {
        echo "<br /><br />";
    }



        $zoneToSelect = false;
    if (!empty($aPublishers)) {
        MAX_sortArray($aPublishers, ($listorder == 'id' ? 'publisher_id' : $listorder), $orderdirection == 'up');
        $i=0;

        //select all checkboxes
        $publisherIdList = '';
        foreach ($aPublishers as $publisherId => $aPublisher) {
            $publisherIdList .= $publisherId . '|';
        }

        echo"<input type='checkbox' id='selectAllField' onClick='toggleAllZones(\"".$publisherIdList."\");'><label for='selectAllField'>".$strSelectUnselectAll."</label>";

        foreach ($aPublishers as $publisherId => $aPublisher) {
            $publisherName = $aPublisher['name'];
		    $aZones = Admin_DA::getZones($aParams + array('publisher_id' => $publisherId), true);


/* openxmods - DAC009 */


$_banner_query = mysql_query("select * from ".$table_prefix."banners where masterbanner = '-2' and bannerid = ".$bannerId."");


	    if(mysql_num_rows($_banner_query)>0){



			foreach($aZones as $key => $_zones) {

		$_query = mysql_query("select * from ".$table_prefix."zones where masterzone = '-2' and zoneid = ".$key."") or die("Error-->1".mysql_error());

			     if(mysql_num_rows($_query) == 0) {

				unset($aZones[$key]); 

			    }



			}
	   } else {


   	       		$_banner_query = mysql_query("select * from ".$table_prefix."banners where bannerid = ".$bannerId."")or die("Error-->in else 8".mysql_error());

		$value=mysql_fetch_array($_banner_query);
 	    
	   // if(mysql_num_rows($_banner_query)>0){

		if($value['masterbanner'] == '-3') {


		
		     foreach($aZones as $key => $_zones) {


		$_query = mysql_query("select * from ".$table_prefix."zones where masterzone = '-3' and zoneid = ".$key."") or die("Error-->1".mysql_error());

			     if(mysql_num_rows($_query) == 0) {

				unset($aZones[$key]); 

			    }

		    }
		}
		else if($value['masterbanner'] == '-4')
              {   //mobile html banner



		     foreach($aZones as $key => $_zones) {


		$_query = mysql_query("select * from ".$table_prefix."zones where masterzone = '-4' and zoneid = ".$key."") or die("Error-->1".mysql_error());

			     if(mysql_num_rows($_query) == 0) {

				unset($aZones[$key]); 

			    }

		    }

                }


else {
		

				     foreach($aZones as $key => $_zones) {


		$_query = mysql_query("select * from ".$table_prefix."zones where masterzone = '-1' and zoneid = ".$key."") or die("Error-->1".mysql_error());

			     if(mysql_num_rows($_query) == 0) {

				unset($aZones[$key]); 

			    }

		    }	
		
		}
	   }




/* openxmods - DAC009 */






            if (!empty($aZones)) {

	        $zoneToSelect = true;
                $bgcolor = ($i % 2 == 0) ? " bgcolor='#F6F6F6'" : '';
                $bgcolorSave = $bgcolor;

                $allchecked = true;
                foreach ($aZones as $zoneId => $aZone) {
                    if (!isset($aLinkedZones[$zoneId])) {
                        $allchecked = false;
                        break;
                    }
                }
                $checked = $allchecked ? ' checked' : '';
                if ($i > 0) echo "
<tr height='1'>
    <td colspan='3' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td>
</tr>";
                echo "
<tr height='25'$bgcolor>
    <td>
        <table>
            <tr>
                <td>&nbsp;</td>
                <td valign='top'><input id='affiliate$publisherId' name='affiliate[$publisherId]' type='checkbox' value='t'$checked onClick='toggleZones($publisherId);' tabindex='$tabindex'>&nbsp;&nbsp;</td>
                <td valign='top'><img src='" . OX::assetPath() . "/images/icon-affiliate.gif' align='absmiddle'>&nbsp;</td>
                <td><a href='affiliate-edit.php?affiliateid=$publisherId'>".htmlspecialchars($publisherName)."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </td>
    <td>$publisherId</td>
    <td height='25'>&nbsp;</td>
</tr>";

                $tabindex++;
                if (!empty($aZones)) {
                    MAX_sortArray($aZones, ($listorder == 'id' ? 'zone_id' : $listorder), $orderdirection == 'up');

                   foreach($aZones as $zoneId => $aZone) {


                        $zoneName = $aZone['name'];
                        $zoneDescription = $aZone['description'];
                        $zoneIsActive = (isset($aZone['active']) && $aZone['active'] == 't') ? true : false;
                        $zoneIcon = MAX_getEntityIcon('zone', $zoneIsActive, $aZone['type']);
                        $checked = isset($aLinkedZones[$zoneId]) ? ' checked' : '';
                        $bgcolor = ($checked == ' checked') ? " bgcolor='#d8d8ff'" : $bgcolorSave;



		$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
		$GLOBALS['_MAX']['CONF']['database']['password']);
		mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
		$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


$bannerid = $_GET['bannerid'];
$query = mysql_query("select * from ".$table_prefix."banners where bannerid = ".$bannerid."") or die(mysql_error());
$query_value = mysql_fetch_array($query);
$masterbanner = $query_value['masterbanner'];
$banner = $_query_value['bannerid'];



if($masterbanner == '-1')
{


                        echo "
<tr height='25'$bgcolor>
    <td>
        <table>
            <tr>
                <td width='28'>&nbsp;</td>
                <td valign='top'><input name='includezone[$zoneId]' id='a$publisherId' type='checkbox' value='t'$checked onClick='toggleAffiliate($publisherId);' tabindex='$tabindex'>&nbsp;&nbsp;</td>
                <td valign='top'><img src='$zoneIcon' align='absmiddle'>&nbsp;</td>
                <td><a href='zone-edit.php?affiliateid=$publisherId&zoneid=$zoneId'>".htmlspecialchars($zoneName)."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </td>
    <td>$zoneId</td>
    <td>".htmlspecialchars($zoneDescription)."</td>
</tr>";
}

else
{

         echo "
<tr height='25'$bgcolor>
    <td>
        <table>
            <tr>
                <td width='28'>&nbsp;</td>
                <td valign='top'><input name='includezone[$zoneId]' id='a$publisherId' type='checkbox' value='t'$checked onClick='toggleAffiliate($publisherId);' tabindex='$tabindex'>&nbsp;&nbsp;</td>
                <td valign='top'><img src='$zoneIcon' align='absmiddle'>&nbsp;</td>
                <td><a href='mobilezone-edit.php?affiliateid=$publisherId&zoneid=$zoneId'>".htmlspecialchars($zoneName)."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </td>
    <td>$zoneId</td>
    <td>".htmlspecialchars($zoneDescription)."</td>
</tr>";
}
                    }
                }
                $i++;
            }
        }
        echo "
<tr height='1'><td colspan='3' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>";
    }
    if (!$zoneToSelect) {
        echo "
<tr height='25' bgcolor='#F6F6F6'>
    <td colspan='4'>&nbsp;&nbsp;{$GLOBALS['strNoZonesToLinkToCampaign']}</td>
</tr>
<tr height='1'><td colspan='3' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>";
    }

    echo "
</table>";

        echo "
<br /><br />
<input type='submit' name='submit' value='{$GLOBALS['strSaveChanges']}' tabindex='$tabindex'>";
        $tabindex++;

    echo "
</form>";

    /*-------------------------------------------------------*/
    /* Form requirements                                     */
    /*-------------------------------------------------------*/

    ?>

    <script language='Javascript'>
    <!--
        affiliates = new Array();
    <?php
        if (!empty($aPublishersZones)) {
            foreach ($aPublishersZones as $publisherId => $aPublishersZone) {
                if (!empty($aPublishersZone['children'])) {
                    $num = count($aPublishersZone['children']);
                    echo "
affiliates[$publisherId] = $num;";
                }
            }
        }
    ?>

        function toggleAffiliate(affiliateid)
        {
            var count = 0;
            var affiliate;

            for (var i=0; i<document.zones.elements.length; i++)
            {
                if (document.zones.elements[i].name == 'affiliate[' + affiliateid + ']')
                    affiliate = i;

                if (document.zones.elements[i].id == 'a' + affiliateid + '' &&
                    document.zones.elements[i].checked)
                    count++;
            }

            document.zones.elements[affiliate].checked = (count == affiliates[affiliateid]);
        }

        function toggleZones(affiliateid)
        {
            var checked

            for (var i=0; i<document.zones.elements.length; i++)
            {
                if (document.zones.elements[i].name == 'affiliate[' + affiliateid + ']')
                    checked = document.zones.elements[i].checked;

                if (document.zones.elements[i].id == 'a' + affiliateid + '')
                    document.zones.elements[i].checked = checked;
            }
        }

        function toggleAllZones(zonesList)
        {
            var zonesArray, checked, selectAllField;

            selectAllField = document.getElementById('selectAllField');

            zonesArray = zonesList.split('|');

            for (var i=0; i<document.zones.elements.length; i++) {

                if (selectAllField.checked == true) {
                    document.zones.elements[i].checked = true;
                } else {
                    document.zones.elements[i].checked = false;
                }
            }
        }

    //-->
    </script>

<?php

    /*-------------------------------------------------------*/
    /* Store preferences                                     */
    /*-------------------------------------------------------*/

    $session['prefs'][$pageName]['listorder'] = $listorder;
    $session['prefs'][$pageName]['orderdirection'] = $orderdirection;

    phpAds_SessionDataStore();

    /*-------------------------------------------------------*/
    /* HTML framework                                        */
    /*-------------------------------------------------------*/

    phpAds_PageFooter();

?>
