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
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';

/**
 * A Client delivery limitation plugin, for filtering delivery of ads on the
 * basis of the viewer's operating system.
 *
 * Works with:
 * A comma separated string of operating system codes. See the Os.res.inc.php
 * resource file for details of the valid operating system codes.
 *
 * Valid comparison operators:
 * =~, !~
 *
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Andrew Hill <andrew.hill@openx.org>
 * @author     Chris Nutting <chris.nutting@openx.org>
 * @author     Andrzej Swedrzynski <andrzej.swedrzynski@openx.org>
 */
class Plugins_DeliveryLimitations_Client_InApp extends Plugins_DeliveryLimitations_CommaSeparatedData
{
    function __construct()
    {
        parent::__construct();
	$this->aOperations = array(
            '==' => $GLOBALS['strEqualTo'],
            '!=' => $GLOBALS['strDifferentFrom']
    );
        $this->nameEnglish = 'InApp - Advertising';
    }
    
    function init($data)
    {
        parent::init($data);
        $this->setAValues(array_keys($this->res));
    }

    /**
     * Return if this plugin is available in the current context
     *
     * @return boolean
     */
    function isAllowed()
    {
        return !empty($GLOBALS['_MAX']['CONF']['Client']['sniff']);
    }

    /**
     * Outputs the HTML to display the data for this limitation
     *
     * @return void
     */
    function displayArrayData()
    {
        $tabindex =& $GLOBALS['tabindex'];

		echo "<table cellpadding='3' cellspacing='3'>";
		foreach ($this->res as $key => $value) {
			if ($i % 4 == 0) echo "<tr>";
			echo "<td><input type='radio' name='acl[{$this->executionorder}][data][]' value='$key'".(in_array($key, $this->data) ? ' checked="checked"' : '')." tabindex='".($tabindex++)."'>".ucfirst($value)."</td>";
			if (($i + 1) % 4 == 0) echo "</tr>";
			$i++;
		}
		if (($i + 1) % 4 != 0) echo "</tr>";
		echo "</table>";
    }

}

?>
