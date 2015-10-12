<?php

/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                                                |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: demoLimitation.class.php 33995 2009-03-18 23:04:15Z chris.nutting $
*/

require_once LIB_PATH . '/Extension/deliveryLimitations/DeliveryLimitationsCommaSeparatedData.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';

/**
 * A Client delivery limitation plugin, for filtering delivery of ads on the
 * basis of the viewer's IP address.
 *
 * Works with:
 * A string that describes a valid IP address, or a range or IP addresses, eg:
 *   10.0.0.*
 *
 * Valid comparison operators:
 * ==, !=
 *
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Andrew Hill <andrew@m3.net>
 * @author     Chris Nutting <chris@m3.net>
 * @author     Andrzej Swedrzynski <andrzej.swedrzynski@m3.net>
 */
class Plugins_DeliveryLimitations_MobileCarrierLimitation_MobileCarrierLimitation extends  Plugins_DeliveryLimitations_CommaSeparatedData
{

    function isAllowed()
    {
        return true;
    }

    function getName()
    {
        return $this->translate('Network - Mobile Carrier');
    }
    

    
     function displayData()
    {
        $this->data = $this->_expandData($this->data);
        $tabindex =& $GLOBALS['tabindex'];

			$resu = explode(',', $this->data[0]);
        // The region plugin is slightly different since we need to allow for multiple regions in different countries
        echo "
            <table border='0' cellpadding='2'>
                <tr>
                    <th>" . $this->translate('Select Country') . "</th>
                    <td>
                        <select name='acl[{$this->executionorder}][data][]' {$disabled}>";
                        foreach ($this->res as $countryCode => $countryName) {
                            //if (count($countryName) === 1) { continue; }
                            $selected = ($resu[0] == $countryCode) ? 'selected="selected"' : '';
                            echo "<option value='{$countryCode}' {$selected}>{$countryName}</option>";
                        }
                        echo "
                        </select>
                    &nbsp;<input type='image' name='action[none]' src='" . OX::assetPath() . "/images/{$GLOBALS['phpAds_TextDirection']}/go_blue.gif' border='0' align='absmiddle' alt='{$GLOBALS['strSave']}'></td>
                </tr>";

        if (!empty($this->data[0])) {

			require_once '../../init.php';


			$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
			$GLOBALS['_MAX']['CONF']['database']['password']);
			mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
			$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
			

			
//echo "select carriername,id from ".$table_prefix."carrier_detail where country = '".$resu['0']."' ORDER BY country";

			$sel1 = mysql_query("select carriername,id from ".$table_prefix."carrier_detail where country = '".$resu[0]."' ORDER BY country");
			$result[] = $resu[0];
///echo $this->data[0];
			while($sele1 = mysql_fetch_array($sel1)){

			$key = $sele1['id'];
			$result[$key] = $sele1['carriername'];


			}

            echo "<tr><th>" . $this->translate('Select Carriers') . "</th><td><div class='box'>";
            //print_r($result);
            $carriers = $result;
            unset($carriers[0]);


   			if(count($this->data) > 1)          
            $selectedcarriers = $this->data;
            		else
            $selectedcarriers = $resu;		
            //print_r($selectedcarriers);
            unset ($selectedcarriers[0]);
            foreach ($carriers as $sCode => $sName) {
                echo "<div class='boxrow'>";
                echo "<input tabindex='".($tabindex++)."' ";
                echo "type='checkbox' id='c_{$this->executionorder}_{$sCode}' name='acl[{$this->executionorder}][data][]' value='{$sCode}'".(in_array($sCode, $selectedcarriers) ? ' CHECKED' : '').">{$sName}</div>";
            }
            echo "</div></td></tr>";
        }
        echo "
            </table>
        ";
        $this->data = $this->_flattenData($this->data);
    }

    /**
     * A private method to "flatten" a delivery limitation into the string format that is
     * saved to the database (either in the acls, acls_channel or banners table, when part
     * of a compiled limitation string).
     *
     * Flattens the country and region array into string format.
     *
     * @access private
     * @param mixed $data An optional, expanded form delivery limitation.
     * @return string The delivery limitation in flattened format.
     */
    function _flattenData($data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        if (is_array($data)) {
            $country = $data[0];
            unset($data[0]);
            return $country . '|' . implode(',', $data);

        }
        return $data;
    }

    /**
     * A private method to "expand" a delivery limitation from the string format that
     * is saved in the database (ie. in the acls or acls_channel table) into its
     * "expanded" form.
     *
     * Expands the string format into an array with the country code in the first
     * element, and the region codes in the remaining elements.
     *
     * @access private
     * @param string $data An optional, flat form delivery limitation data string.
     * @return mixed The delivery limitation data in expanded format.
     */
    function _expandData($data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        if (!is_array($data)) {
            $aData = strlen($data) ? explode('|', $data) : array();
            $aRegions = MAX_limitationsGetAFromS($aData[1]);
            return array_merge(array($aData[0]), $aRegions);
        }
        return $data;
    }

 
    
}

?>
