<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
$Id: limitations.delivery.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * This package contains various utility functions used by Delivery Limitation
 * plugins.
 *
 * @package    MaxDelivery
 * @subpackage limitations
 * @author     Andrzej Swedrzynski <andrzej.swedrzynski@m3.net>
 * @todo Move all the functions used by delivery part of delivery limitation
 * plugins from here to limitations.php. Move other functions to relevant
 * files, possibly creating a few libraries on the way.
 */

/**
 * The shortcut for {@link MAX_limitationsMatchString} with
 * $namespace set to 'CLIENT_GEO'. Useful for Geo delivery
 * limitation plugins.
 *
 * @param string $paramName
 * @param string $limitation
 * @param string $op
 * @param string $aParams
 * @return boolean
 * @see MAX_limitationsMatchString
 */
function MAX_limitationsMatchStringClientGeo($paramName, $limitation, $op, $aParams = array())
{
    return MAX_limitationsMatchString($paramName, $limitation, $op, $aParams, 'CLIENT_GEO');
}

function Dot2LongIP($IPaddr) 	
{ 		 		
		if ($IPaddr == "")
		{
				return 0;
		}
		else
		{
				$ips = explode(".", "$IPaddr");
				return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
		} 
}
	

function MAX_cacheGetgeo($ip, $cached = true)
{
	if(!isset($_COOKIE['ip2_details']))
	{
		 
		$cookietime=30;
		/*$IP=Dot2LongIP($ip);
		$res=OA_Dal_Delivery_query("SELECT lattitude,country_code,longitude FROM djax_geodata_vietnam WHERE '".$IP."'   BETWEEN  ip_start and ip_end");
			while($row=OA_Dal_Delivery_fetchAssoc($res))
			{
			$latitude=$row['lattitude'];
			$longitude=$row['longitude'];
			$countrycode=$row['country_code'];
			}*/
			$aRows =$GLOBALS['_MAX']['CLIENT_GEO'];
			//print_r($GLOBALS['_MAX']);
			$value = $aRows['country_code'].'_'.$aRows['latitude'].'_'.$aRows['longitude'];
			$name = 'ip2_details';
			setcookie($name, $value,time()+60*60*24*$cookietime);
			$aRowsdata =$GLOBALS['_MAX']['CLIENT_GEO'];
			$aRowsdata['latitude']=$aRows['latitude'];
			$aRowsdata['longitude']=$aRows['longitude'];
			
			
		//DAC024 latitude and longitude from  IP2Location
	}
	else
	{ 
		$v=explode("_",$_COOKIE['ip2_details']);
		$aRowsdata['country_code'] = $v[0];
		$aRowsdata['latitude'] = $v[1];
		$aRowsdata['longitude'] = $v[2];
	
	}


	return $aRowsdata;
}
////DAC015-Location Targeting

function  MAX_limitationsMatchArrayClientGeoip($limitation, $op, &$aParams = array())
{
	
	//die("Hi");


	$loc_id=explode('|',$limitation);

	$locid=implode(',',$loc_id);
	

	$query=OA_Dal_Delivery_query("select * from ox_akanetwork_cities where locid IN($locid)");

	unset($latitude);
	unset($longitude);
	unset($diameter);
	$geodatadetails=MAX_cacheGetgeo($_SERVER['REMOTE_ADDR']);
	$latitude=$geodatadetails['latitude'];
	$longitude=$geodatadetails['longitude'];

	while($fetchrows= OA_Dal_Delivery_fetchAssoc($query))
	{
	$temp=0.28;//mean value to get approximate distance
	$rad=$fetchrows['Diameter'];
	// we'll want everything within, say, 10km distance
	$distance =$rad-($rad*$temp);
	$radius = 6371;// earth's radius in km = ~6371
	$lat=$fetchrows['latitude'];
	$lng=$fetchrows['longitude'];
	$latitude_min=$lat - rad2deg($distance / $radius);
	$latitude_max=$lat + rad2deg($distance / $radius);
	$longitude_min=$lng - rad2deg($distance / $radius / cos(deg2rad($lat)));
	$longitude_max=$lng + rad2deg($distance / $radius / cos(deg2rad($lat)));

		if(($latitude>=$latitude_min && $latitude<=$latitude_max) && ($longitude>=$longitude_min && $longitude<=$longitude_max))
		{
			$count=1;
		}

	}

	if($count=='1')
	{
		$result='1';
	}
	else
	{
		$result='0';
	
	}
//	echo $result;
	  
	if ($op == '=~')
	{
            return $result;
        }
	else if ($op == '!~')
	{
            return !$result;
        }

}





/**
 * The utility function which checks if the value in the parameters
 * in the request for an ad fulfill the requirements of delivery limitation.
 * This function checks string values.
 * The parameters are looked in the $aParam array. If the $aParam array
 * is empty or unspecified, then $GLOBALS['_MAX']['$namespace'] array
 * is used instead.
 *
 * @param string $paramName Name of the parameter to look for in an array.
 * @param string $limitation Value to be matched with.
 * @param string $op The operator used to compare strings.
 * @param string $aParams The array in which the value is looked for.
 * @param string $namespace The namespace in the $GLOBALS['_MAX'] array used
 *               if when $aParams is empty.
 * @return boolean True if the parameters fulfill the limitations, false
 *                 otherwise.
 * @see MAX_limitationsMatchStringClientGeo
 * @see MAX_limitationsMatchStringValue
 */
function MAX_limitationsMatchString(
    $paramName, $limitation, $op, $aParams = array(), $namespace = 'CLIENT')
{
    if ($limitation == '') {
        return true;
    }
    if (empty($aParams)) {
        $aParams = $GLOBALS['_MAX'][$namespace];
    }

    $value = $aParams[$paramName];

    return MAX_limitationsMatchStringValue($value, $limitation, $op);
}

/**
 * Match a numeric value (greater than or less than)
 *
 * @param string $paramName Name of the parameter to look for in an array.
 * @param string $limitation Value to be matched with.
 * @param string $op The operator used to compare strings.
 * @param string $aParams The array in which the value is looked for.
 * @param string $namespace The namespace in the $GLOBALS['_MAX'] array used
 *               if when $aParams is empty.
 * @return boolean True if the parameters fulfill the limitations, false
 *                 otherwise.
 *
 * @author Mohammed El-Hakim
 * @author     Chris Nutting <chris.nutting@openx.org>
 */
function MAX_limitationMatchNumeric(
    $paramName, $limitation, $op, $aParams = array(), $namespace = 'CLIENT')
{
    if ($limitation == '') {
        return !MAX_limitationsIsOperatorPositive($op);
    }
    if (empty($aParams)) {
        $aParams = $GLOBALS['_MAX'][$namespace];
    }

	if (!isset($aParams[$paramName]) || !is_numeric($aParams[$paramName]) || !is_numeric($limitation)) {
		return !MAX_limitationsIsOperatorPositive($op);
	} else {
	    $value = $aParams[$paramName];
	}

	if ($op == 'lt'){
    	return $value < $limitation;
    } else if ($op == 'gt'){
    	return $value > $limitation;
    } else {
    	return !MAX_limitationsIsOperatorPositive($op);
    }
}

/**
 * An utility function which matches the $value with $limitation
 * using $op operator.
 *
 * The possible operators are:
 * <ul>
 *   <li>==: true iff $value and $limitation are exactly the same</li>
 *   <li>!=: true iff $value and $limitation are different</li>
 *   <li>=~: true iff $value contains $limitation</li>
 *   <li>!=: true iff $value does not contain $limitation</li>
 *   <li>=x: true iff $value matches regular expression $limitation</li>
 *   <li>!x: true iff $value does not match regular expression $limitation</li>
 * </ul>
 *
 * @param string $value
 * @param string $limitation
 * @param string $op
 * @return boolean True if the $value matches the limitation,
 * false otherwise.
 */



 //======================================================Modified By DAC021============================================//
function profileLimit($value, $limitation, $op)
{

    $limitation = strtolower($limitation);
    $value = strtolower($value);

        if ($op == '<='){
            return $value <= $limitation;
    } elseif ($op == '>='){
            return $value >= $limitation;
    } elseif($op == '==') {
        return $limitation == $value;
    } elseif ($op == '!=') {
        return $limitation != $value;
    } elseif ($op == '=~') {
        return MAX_stringContains($value, $limitation);
    } elseif ($op == '!~') {
        return !MAX_stringContains($value, $limitation);
    }
}


function ModelName_MatchStringValue($value, $limitation, $op)
{
    $limitation = strtolower($limitation);
    $value = strtolower($value);


    if ($op == '==') {
        return $limitation == $value;
    } elseif ($op == '!=') {
        return $limitation != $value;
    } elseif ($op == '=~') {
        return MAX_stringContains($limitation, $value);
    } elseif ($op == '!~') {
        return !MAX_stringContains($limitation, $value);
    } elseif ($op == '=x') {
        return preg_match(_getSRegexpDelimited($limitation), $value);
    } else {
        return !preg_match(_getSRegexpDelimited($limitation), $value);
    }
}



//======================================================Modified By DAC021============================================//



function MAX_limitationsMatchStringValue($value, $limitation, $op)
{
    $limitation = strtolower($limitation);
    $value = strtolower($value);

    if ($op == '==') {
        return $limitation == $value;
    } elseif ($op == '!=') {
        return $limitation != $value;
    } elseif ($op == '=~') {
        return MAX_stringContains($value, $limitation);
    } elseif ($op == '!~') {
        return !MAX_stringContains($value, $limitation);
    } elseif ($op == '=x') {
        return preg_match(_getSRegexpDelimited($limitation), $value);
    } else {
        return !preg_match(_getSRegexpDelimited($limitation), $value);
    }
}

/**
 * An utility function which checks if the value in the array matches
 * the limitation specified in the $limitation and $op arguments.
 * Uses $GLOBALS['_MAX']['CLIENT_GEO'] array if $aParams is empty.
 * For details on how matching is done see
 * {@link MAX_limitationsMatchArrayValue}.
 *
 * @param string $paramName
 * @param string $limitation
 * @param string $op
 * @param array $aParams
 * @return boolean true if the value matches the limitation, false otherwise.
 */
function MAX_limitationsMatchArrayClientGeo($paramName, $limitation, $op, &$aParams = array())
{
    return MAX_limitationsMatchArray($paramName, $limitation, $op, $aParams, 'CLIENT_GEO');
}

/**
 * An utility function which checks if the value in $aParams[$paramName]
 * matches the array limitations specified in $limitation and $op.
 * If $aParams is empty then $GLOBALS['_MAX'][$namespace] is used instead.
 * See {@link MAX_limitationsMatchArrayValue} for more details on
 * how matching is done.
 *
 * @param string $paramName
 * @param string $limitation
 * @param string $op
 * @param string $aParams
 * @param string $namespace
 * @return boolean True if the value matches the limitations, false otherwise.
 */
function MAX_limitationsMatchArray($paramName, $limitation, $op, $aParams = array(), $namespace='CLIENT')
{
    if (empty($aParams)) {
        $aParams = $GLOBALS['_MAX'][$namespace];
    }
    if ($limitation == '' || empty($aParams[$paramName])) {
        return !MAX_limitationsIsOperatorPositive($op);
    }

    return MAX_limitationsMatchArrayValue($aParams[$paramName], $limitation, $op);
}

/**
 * An utility function which checks if the array specified in the $value
 * matches the limitation specified in the $limitation and $op variables.
 * The $value is supposed to be a single string and $limitation is
 * a list of values separated by `,' character.
 *
 * The function returns true if the $value matches the limitation,
 * false otherwise.
 *
 * The meaning of $op is the following:
 * <ul>
 *   <li>==: true iff $limitation consists of single value and this value
 *     is exactly the same as $value.</li>
 *   <li>=~: true iff $value is a member of the $limitation array.</li>
 *   <li>!~: true iff $value is not a member of the $limitation array.</li>
 * </ul>
 *
 * @param string $value Value to check against the limitation.
 * @param string $limitation The limitation specification as a string.
 * @param string $op The operator to use to apply the limitation.
 * @return boolean True if the $value matches the limitation,
 * false otherwise.
 */
function MAX_limitationsMatchArrayValue($value, $limitation, $op)
{
    if ($op == '==') {
        return strcasecmp($limitation, $value) == 0;
    } else if ($op == '=~') {
        if ($value == '') {
            return true;
        }
        return stripos(','.$limitation.',', ','.$value.',') !== false;
    } else {
        if ($value == '') {
            return false;
        }
        return stripos(','.$limitation.',', ','.$value.',') === false;
    }
}

/**
 * Returns true if $op is one of the simple operators: either '==' or '!=',
 * false otherwise.
 *
 * @param string $op The operator to be checked.
 * @return boolean True if $op is either '==' or '!=', false otherwise.
 */
function MAX_limitationsIsOperatorSimple($op)
{
    return $op == '==' || $op == '!=';
}

/**
 * Returns true if $op is one of the contains operators: either '=~' or '!~',
 * false otherwise.
 *
 * @param string $op The operator to be checked.
 * @return boolean True if $op is either '=~' or '!~', false otherwise.
 */
function MAX_limitationsIsOperatorContains($op)
{
    return $op == '=~' || $op == '!~';
}

function MAX_limitationsIsOperatorNumeric($op)
{
    return $op == 'gt' || $op == 'lt' ;
}

/**
 * Returns true if $op is one of the simple operators: either '=x' or '!x',
 * false otherwise.
 *
 * @param string $op The operator to be checked.
 * @return boolean True if $op is either '=x' or '!x', false otherwise.
 */
function MAX_limitationsIsOperatorRegexp($op)
{
    return $op == '=x' || $op == '!x';
}

/**
 * Returns true if $op is one of the positive operators: '==', '=~' or '=x',
 * false otherwise.
 *
 * @param string $op The operator to be checked.
 * @return boolean True if $op is '==', '=~' or '=x', false otherwise.
 */
function MAX_limitationsIsOperatorPositive($op)
{
    return $op == '==' || $op == '=~' || $op == '=x' || $op == 'gt' || $op == 'lt';
}

/**
 * Returns an array where the keys are delivery limitation plugins operators
 * suitable for numeric tests and the values are properly translated strings which
 * describe these operators to the user.
 *
 * @param DeliveryLimitationPlugin $oPlugin
 * @return array Array associating operators with their localized names.
 * @author By Mohammed El-Hakim
 */
function MAX_limitationsGetAOperationsForNumeric($oPlugin)
{
    return array(
        'lt'  => $GLOBALS['strLessThan'],
        'gt'  => $GLOBALS['strGreaterThan'],
    );
}

/**
 * Returns an array where the keys are delivery limitation plugins operators
 * suitable for strings and the values are properly translated strings which
 * describe these operators to the user.
 *
 * @param DeliveryLimitationPlugin $oPlugin
 * @return array Array associating operators with their localized names.
 */
function MAX_limitationsGetAOperationsForString($oPlugin)
{
    return array(
        '==' => $GLOBALS['strEqualTo'],
        '!=' => $GLOBALS['strDifferentFrom'],
        '=~' => MAX_Plugin_Translation::translate('Contains', $oPlugin->module, $oPlugin->package),
        '!~' => MAX_Plugin_Translation::translate('Does not contain', $oPlugin->module, $oPlugin->package),
        '=x' => MAX_Plugin_Translation::translate('Regex match', $oPlugin->module, $oPlugin->package),
        '!x' => MAX_Plugin_Translation::translate('Regex does not match', $oPlugin->module, $oPlugin->package)
    );
}

// STRING UTILITY FUNCTIONS

/**
 * Returns true if $sString contains $sToken, false otherwise.
 *
 * @param string $sString String to be checked.
 * @param unknown_type $sToken String to be contained.
 * @return boolean true if $sString contains $sToken, false otherwise.
 */
function MAX_stringContains($sString, $sToken)
{
    return strpos($sString, $sToken) !== false;
}

/**
 * Returns an array created by exploding string via ','
 * or empty array if the string is empty.
 *
 * @param string $sString String to be exploded.
 * @return array An array exploded from the string.
 */
function MAX_limitationsGetAFromS($sString)
{
    return strlen($sString) ? explode(',', $sString) : array();
}

/**
 * Returns a string created by imploding string via ','
 * or empty string if the array is empty.
 *
 * @param array $aArray An array to implode.
 * @return string A string imploded from the array.
 */
function MAX_limitationsGetSFromA($aArray)
{
    return is_array($aArray) ? implode(',', $aArray) : '';
}

// SQL UTILITY FUNCTIONS

/**
 * Returns a string preprocessed to be used properly in the
 * database comparisons: trimmed and lowercased.
 *
 * @param string $sString
 * @return string Preprocessed string value.
 */
function MAX_limitationsGetPreprocessedString($sString)
{
    return strtolower(trim($sString));
}

/**
 * Returns an array of preprocessed strings. Each string
 * is preprocessed with {@link MAX_limitationsGetPreprocessedString}
 * function.
 *
 * @param array $aArray Array of strings.
 * @return array Array of preprocessed string.
 */
function MAX_limitationsGetPreprocessedArray($aArray) {
    $aItems = array();
    foreach ($aArray as $key => $sItem) {
        $aItems[$key] = MAX_limitationsGetPreprocessedString($sItem);
    }
    return $aItems;
}

// COUNTRY DELIVERY LIMITATION PLUGIN functions

/**
 * Returns an element of the array which represents country.
 * Used by a few of Geo delivery limitation plugins.
 *
 * @param array $aData An array with Geo data.
 * @return string The country.
 */
function MAX_limitationsGetCountry($aData)
{
    return $aData[0];
}

/**
 * Sets a value of the element of the array which represents
 * the country in the array data in some Geo delivery limitation plugins.
 *
 * @param array $aData The array in which the country should be set.
 * @param string $sCountry The value to which country should be set.
 */
function MAX_limitationsSetCountry(&$aData, $sCountry)
{
    $aData[0] = $sCountry;
}

/**
 * Returns the string delimited with '#' character.
 * All '#' characters within a string are escaped
 * with a '\'. Thus, the resulting string can be used
 * as a pattern in preg functions, like preg_match.
 *
 * @param string $sRawRegexp Non-delimited regular expression.
 * @return string Regular expression delimited with '#'.
 */
function _getSRegexpDelimited($sRawRegexp)
{
    return '#' . str_replace('#', '\\#', $sRawRegexp) . '#';
}

/* *****
   IP functions
   ***** */


/**
 * Returns a string representing IP with the last number replaced by the star
 * '*' character.
 *
 * @param string $ip The IP address specified in the 'dot' format.
 * @return string The IP address with last part replaced by '*'.
 */
function MAX_ipWithLastComponentReplacedByStar($ip)
{
    return substr($ip, 0, strrpos($ip, '.') + 1) . '*';
}


/**
 * Returns true if the string contains the start '*' character,
 * false otherwise.
 *
 * @param string $ip A string to check.
 * @return true if the string contains the start '*' character,
 * false otherwise.
 */
function MAX_ipContainsStar($ip)
{
    return MAX_stringContains($ip, '*');
}


?>
