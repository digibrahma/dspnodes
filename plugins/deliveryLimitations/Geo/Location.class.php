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

require_once LIB_PATH . '/Extension/deliveryLimitations/DeliveryLimitationsCommaSeparatedData.php';
require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

/**
 * A Geo delivery limitation plugin, for filtering delivery of ads on the
 * basis of the viewer's country.
 *
 * Works with:
 * A comma separated list of valid country codes. See the Country.res.inc.php
 * resource file for details of the valid country codes.
 *
 * Valid comparison operators:
 * =~, !~
 *
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Andrew Hill <andrew@m3.net>
 * @author     Chris Nutting <chris@m3.net>
 *
 * @TODO Does this need to be updated to use =~ and !~ comparison operators?
 */
class Plugins_DeliveryLimitations_Geo_Location extends Plugins_DeliveryLimitations_CommaSeparatedData
{
    function __construct()
    {
        parent::__construct();
        $this->nameEnglish = 'Geo - Location';
    }

    /**
     * Outputs the HTML to display the data for this limitation
     *
     * @return void
     */
    function displayArrayData()
    {

		 $table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
		$query=OA_Dal_Delivery_query("select * from ox_akanetwork_cities");
		
		while($results=OA_Dal_Delivery_fetchAssoc($query))
		{
		$res[$results['locid']]=$results['location_name'];
		}
        $tabindex =& $GLOBALS['tabindex'];
        echo "<div class='box'>";
        foreach ($res as $code => $name)
	{
            echo "<div class='boxrow'>";
            echo "<input tabindex='".($tabindex++)."' ";
            echo "type='checkbox' id='c_{$this->executionorder}_{$code}' name='acl[{$this->executionorder}][data][]' value='{$code}'".(in_array($code, $this->data) ? ' CHECKED' : '').">{$name}</div>";
        }
        echo "</div>";
    }
}

?>
