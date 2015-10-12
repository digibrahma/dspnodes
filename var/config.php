<?php


function parseDeliveryIniFile($configPath = null, $configFile = null, $sections = true)
{
    if (!$configPath)
    {
        $configPath = MAX_PATH . '/var';
    }
    if ($configFile)
    {
        $configFile = '.' . $configFile;
    }
    $host           = OX_getHostName();
    $configFileName = $configPath . '/' . $host . $configFile . '.conf.php';
    $conf           = @parse_ini_file($configFileName, $sections);
    if (isset($conf['realConfig']))
    {
        $realconf = @parse_ini_file(MAX_PATH . '/var/' . $conf['realConfig'] . '.conf.php', $sections);
        $conf     = mergeConfigFiles($realconf, $conf);
    }
    if (!empty($conf))
    {
        return $conf;
    }
    elseif ($configFile === '.plugin')
    {
        $pluginType    = basename($configPath);
        $defaultConfig = MAX_PATH . '/plugins/' . $pluginType . '/default.plugin.conf.php';
        $conf          = @parse_ini_file($defaultConfig, $sections);
        if ($conf !== false)
        {
            return $conf;
        }
        echo "Revive Adserver could not read the default configuration file for the {$pluginType} plugin";
        exit(1);
    }
    $configFileName = $configPath . '/default' . $configFile . '.conf.php';
    $conf           = @parse_ini_file($configFileName, $sections);
    if (isset($conf['realConfig']))
    {
        $conf = @parse_ini_file(MAX_PATH . '/var/' . $conf['realConfig'] . '.conf.php', $sections);
    }
    if (!empty($conf))
    {
        return $conf;
    }
    if (file_exists(MAX_PATH . '/var/INSTALLED'))
    {
        echo "Revive Adserver has been installed, but no configuration file was found.\n";
        exit(1);
    }
    echo "Revive Adserver has not been installed yet -- please read the INSTALL.txt file.\n";
    exit(1);
}
if (!function_exists('mergeConfigFiles'))
{
    function mergeConfigFiles($realConfig, $fakeConfig)
    {
        foreach ($fakeConfig as $key => $value)
        {
            if (is_array($value))
            {
                if (!isset($realConfig[$key]))
                {
                    $realConfig[$key] = array();
                }
                $realConfig[$key] = mergeConfigFiles($realConfig[$key], $value);
            }
            else
            {
                if (isset($realConfig[$key]) && is_array($realConfig[$key]))
                {
                    $realConfig[$key][0] = $value;
                }
                else
                {
                    if (isset($realConfig) && !is_array($realConfig))
                    {
                        $temp          = $realConfig;
                        $realConfig    = array();
                        $realConfig[0] = $temp;
                    }
                    $realConfig[$key] = $value;
                }
            }
        }
        unset($realConfig['realConfig']);
        return $realConfig;
    }
}


function OX_getMinimumRequiredMemory($limit = null)
{
    if ($limit == 'maintenance')
    {
        return 134217728;
    }
    return 134217728;
}
function OX_getMemoryLimitSizeInBytes()
{
    $phpMemoryLimit = ini_get('memory_limit');
    if (empty($phpMemoryLimit) || $phpMemoryLimit == -1)
    {
        return -1;
    }
    $aSize                 = array(
        'G' => 1073741824,
        'M' => 1048576,
        'K' => 1024
    );
    $phpMemoryLimitInBytes = $phpMemoryLimit;
    foreach ($aSize as $type => $multiplier)
    {
        $pos = strpos($phpMemoryLimit, $type);
        if (!$pos)
        {
            $pos = strpos($phpMemoryLimit, strtolower($type));
        }
        if ($pos)
        {
            $phpMemoryLimitInBytes = substr($phpMemoryLimit, 0, $pos) * $multiplier;
        }
    }
    return $phpMemoryLimitInBytes;
}
function OX_checkMemoryCanBeSet()
{
    $phpMemoryLimitInBytes = OX_getMemoryLimitSizeInBytes();
    if ($phpMemoryLimitInBytes == -1)
    {
        return true;
    }
    OX_increaseMemoryLimit($phpMemoryLimitInBytes + 1);
    $newPhpMemoryLimitInBytes = OX_getMemoryLimitSizeInBytes();
    $memoryCanBeSet           = ($phpMemoryLimitInBytes != $newPhpMemoryLimitInBytes);
    @ini_set('memory_limit', $phpMemoryLimitInBytes);
    return $memoryCanBeSet;
}
function OX_increaseMemoryLimit($setMemory)
{
    $phpMemoryLimitInBytes = OX_getMemoryLimitSizeInBytes();
    if ($phpMemoryLimitInBytes == -1)
    {
        return true;
    }
    if ($setMemory > $phpMemoryLimitInBytes)
    {
        if (@ini_set('memory_limit', $setMemory) === false)
        {
            return false;
        }
    }
    return true;
}


function setupConfigVariables()
{
    $GLOBALS['_MAX']['MAX_DELIVERY_MULTIPLE_DELIMITER'] = '|';
    $GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX']           = '__';
    $GLOBALS['_MAX']['thread_id']                       = uniqid();
    $GLOBALS['_MAX']['SSL_REQUEST']                     = false;
    if ((!empty($_SERVER['SERVER_PORT']) && !empty($GLOBALS['_MAX']['CONF']['openads']['sslPort']) && ($_SERVER['SERVER_PORT'] == $GLOBALS['_MAX']['CONF']['openads']['sslPort'])) || (!empty($_SERVER['HTTPS']) && ((strtolower($_SERVER['HTTPS']) == 'on') || ($_SERVER['HTTPS'] == 1))) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on')) || (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && (strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) == 'on')) || (!empty($_SERVER['FRONT-END-HTTPS']) && (strtolower($_SERVER['FRONT-END-HTTPS']) == 'on')))
    {
        $GLOBALS['_MAX']['SSL_REQUEST'] = true;
    }
    $GLOBALS['_MAX']['MAX_RAND'] = isset($GLOBALS['_MAX']['CONF']['priority']['randmax']) ? $GLOBALS['_MAX']['CONF']['priority']['randmax'] : 2147483647;
    list($micro_seconds, $seconds) = explode(" ", microtime());
    $GLOBALS['_MAX']['NOW_ms'] = round(1000 * ((float) $micro_seconds + (float) $seconds));
    if (substr($_SERVER['SCRIPT_NAME'], -11) != 'install.php')
    {
        $GLOBALS['serverTimezone'] = date_default_timezone_get();
        OA_setTimeZoneUTC();
    }
}
function setupServerVariables()
{
    if (empty($_SERVER['REQUEST_URI']))
    {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        if (!empty($_SERVER['QUERY_STRING']))
        {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
}
function setupDeliveryConfigVariables()
{
    if (!defined('MAX_PATH'))
    {
        define('MAX_PATH', dirname(dirname(dirname(dirname(__FILE__)))));
    }
    if (!defined('OX_PATH'))
    {
        define('OX_PATH', MAX_PATH);
    }
    if (!defined('RV_PATH'))
    {
        define('RV_PATH', MAX_PATH);
    }
    if (!defined('LIB_PATH'))
    {
        define('LIB_PATH', MAX_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'OX');
    }
    if (!(isset($GLOBALS['_MAX']['CONF'])))
    {
        $GLOBALS['_MAX']['CONF'] = parseDeliveryIniFile();
    }
    setupConfigVariables();
}
function OA_setTimeZone($timezone)
{
    date_default_timezone_set($timezone);
    $GLOBALS['_DATE_TIMEZONE_DEFAULT'] = $timezone;
}
function OA_setTimeZoneUTC()
{
    OA_setTimeZone('UTC');
}
function OA_setTimeZoneLocal()
{
    $tz = !empty($GLOBALS['_MAX']['PREF']['timezone']) ? $GLOBALS['_MAX']['PREF']['timezone'] : 'GMT';
    OA_setTimeZone($tz);
}
function OX_getHostName()
{
    if (!empty($_SERVER['HTTP_HOST']))
    {
        $host = explode(':', $_SERVER['HTTP_HOST']);
        $host = $host[0];
    }
    else if (!empty($_SERVER['SERVER_NAME']))
    {
        $host = explode(':', $_SERVER['SERVER_NAME']);
        $host = $host[0];
    }
    return $host;
}
function OX_getHostNameWithPort()
{
    if (!empty($_SERVER['HTTP_HOST']))
    {
        $host = $_SERVER['HTTP_HOST'];
    }
    else if (!empty($_SERVER['SERVER_NAME']))
    {
        $host = $_SERVER['SERVER_NAME'];
    }
    return $host;
}
function setupIncludePath()
{
    static $checkIfAlreadySet;
    if (isset($checkIfAlreadySet))
    {
        return;
    }
    $checkIfAlreadySet = true;
    $oxPearPath        = MAX_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'pear';
    $oxZendPath        = MAX_PATH . DIRECTORY_SEPARATOR . 'lib';
    set_include_path($oxPearPath . PATH_SEPARATOR . $oxZendPath . PATH_SEPARATOR . get_include_path());
}

OX_increaseMemoryLimit(OX_getMinimumRequiredMemory());
if (!defined('E_DEPRECATED'))
{
    define('E_DEPRECATED', 0);
}
setupServerVariables();
setupDeliveryConfigVariables();
$conf                             = $GLOBALS['_MAX']['CONF'];
$GLOBALS['_OA']['invocationType'] = array_search(basename($_SERVER['SCRIPT_FILENAME']), $conf['file']);
if (!empty($conf['debug']['production']))
{
    error_reporting(E_ALL & ~(E_NOTICE | E_WARNING | E_DEPRECATED | E_STRICT));
}
else
{
    error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT));
}

$file                            = '/lib/max/Delivery/common.php';
$GLOBALS['_MAX']['FILES'][$file] = true;

$file                                                              = '/lib/max/Delivery/cookie.php';
$GLOBALS['_MAX']['FILES'][$file]                                   = true;
$GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'] = array();
if (!is_callable('MAX_cookieSet'))
{
    if (!empty($conf['cookie']['plugin']) && is_readable(MAX_PATH . "/plugins/cookieStorage/{$conf['cookie']['plugin']}.delivery.php"))
    {
        include MAX_PATH . "/plugins/cookieStorage/{$conf['cookie']['plugin']}.delivery.php";
    }
    else
    {
        function MAX_cookieSet($name, $value, $expire, $path = '/', $domain = null)
        {
            return MAX_cookieClientCookieSet($name, $value, $expire, $path, $domain);
        }
        function MAX_cookieUnset($name)
        {
            return MAX_cookieClientCookieUnset($name);
        }
        function MAX_cookieFlush()
        {
            return MAX_cookieClientCookieFlush();
        }
        function MAX_cookieLoad()
        {
            return true;
        }
    }
}
function MAX_cookieAdd($name, $value, $expire = 0)
{
    if (!isset($GLOBALS['_MAX']['COOKIE']['CACHE']))
    {
        $GLOBALS['_MAX']['COOKIE']['CACHE'] = array();
    }
    $GLOBALS['_MAX']['COOKIE']['CACHE'][$name] = array(
        $value,
        $expire
    );
}
function MAX_cookieSetViewerIdAndRedirect($viewerId)
{
    $aConf = $GLOBALS['_MAX']['CONF'];
    MAX_cookieAdd($aConf['var']['viewerId'], $viewerId, _getTimeYearFromNow());
    MAX_cookieFlush();
    if ($GLOBALS['_MAX']['SSL_REQUEST'])
    {
        $url = MAX_commonConstructSecureDeliveryUrl(basename($_SERVER['SCRIPT_NAME']));
    }
    else
    {
        $url = MAX_commonConstructDeliveryUrl(basename($_SERVER['SCRIPT_NAME']));
    }
    $url .= "?{$aConf['var']['cookieTest']}=1&" . $_SERVER['QUERY_STRING'];
    MAX_header("Location: {$url}");
    exit;
}
function _getTimeThirtyDaysFromNow()
{
    return MAX_commonGetTimeNow() + 2592000;
}
function _getTimeYearFromNow()
{
    return MAX_commonGetTimeNow() + 31536000;
}
function _getTimeYearAgo()
{
    return MAX_commonGetTimeNow() - 31536000;
}
function MAX_cookieUnpackCapping()
{
    $conf        = $GLOBALS['_MAX']['CONF'];
    $cookieNames = $GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'];
    if (!is_array($cookieNames))
        return;
    foreach ($cookieNames as $cookieName)
    {
        if (!empty($_COOKIE[$cookieName]))
        {
            if (!is_array($_COOKIE[$cookieName]))
            {
                $output = array();
                $data   = explode('_', $_COOKIE[$cookieName]);
                foreach ($data as $pair)
                {
                    list($name, $value) = explode('.', $pair);
                    $output[$name] = $value;
                }
                $_COOKIE[$cookieName] = $output;
            }
        }
        if (!empty($_COOKIE['_' . $cookieName]) && is_array($_COOKIE['_' . $cookieName]))
        {
            foreach ($_COOKIE['_' . $cookieName] as $adId => $cookie)
            {
                if (_isBlockCookie($cookieName))
                {
                    $_COOKIE[$cookieName][$adId] = $cookie;
                }
                else
                {
                    if (isset($_COOKIE[$cookieName][$adId]))
                    {
                        $_COOKIE[$cookieName][$adId] += $cookie;
                    }
                    else
                    {
                        $_COOKIE[$cookieName][$adId] = $cookie;
                    }
                }
                MAX_cookieUnset("_{$cookieName}[{$adId}]");
            }
        }
    }
}
function _isBlockCookie($cookieName)
{
    return in_array($cookieName, array(
        $GLOBALS['_MAX']['CONF']['var']['blockAd'],
        $GLOBALS['_MAX']['CONF']['var']['blockCampaign'],
        $GLOBALS['_MAX']['CONF']['var']['blockZone'],
        $GLOBALS['_MAX']['CONF']['var']['lastView'],
        $GLOBALS['_MAX']['CONF']['var']['lastClick'],
        $GLOBALS['_MAX']['CONF']['var']['blockLoggingClick']
    ));
}
function MAX_cookieGetUniqueViewerId($create = true)
{
    static $uniqueViewerId = null;
    if (!is_null($uniqueViewerId))
    {
        return $uniqueViewerId;
    }
    $conf = $GLOBALS['_MAX']['CONF'];
    if (isset($_COOKIE[$conf['var']['viewerId']]))
    {
        $uniqueViewerId = $_COOKIE[$conf['var']['viewerId']];
    }
    elseif ($create)
    {
        $uniqueViewerId                           = md5(uniqid('', true));
        $GLOBALS['_MAX']['COOKIE']['newViewerId'] = true;
    }
    return $uniqueViewerId;
}
function MAX_cookieGetCookielessViewerID()
{
    if (empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['HTTP_USER_AGENT']))
    {
        return '';
    }
    $cookiePrefix = $GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX'];
    return $cookiePrefix . substr(md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']), 0, 32 - (strlen($cookiePrefix)));
}
function MAX_Delivery_cookie_cappingOnRequest()
{
    if (isset($GLOBALS['_OA']['invocationType']) && ($GLOBALS['_OA']['invocationType'] == 'xmlrpc' || $GLOBALS['_OA']['invocationType'] == 'view'))
    {
        return true;
    }
    return !$GLOBALS['_MAX']['CONF']['logging']['adImpressions'];
}
function MAX_Delivery_cookie_setCapping($type, $id, $block = 0, $cap = 0, $sessionCap = 0)
{
    $conf     = $GLOBALS['_MAX']['CONF'];
    $setBlock = false;
    if ($cap > 0)
    {
        $expire = MAX_commonGetTimeNow() + $conf['cookie']['permCookieSeconds'];
        if (!isset($_COOKIE[$conf['var']['cap' . $type]][$id]))
        {
            $value    = 1;
            $setBlock = true;
        }
        else if ($_COOKIE[$conf['var']['cap' . $type]][$id] >= $cap)
        {
            $value    = -$_COOKIE[$conf['var']['cap' . $type]][$id] + 1;
            $setBlock = true;
        }
        else
        {
            $value = 1;
        }
        MAX_cookieAdd("_{$conf['var']['cap' . $type]}[{$id}]", $value, $expire);
    }
    if ($sessionCap > 0)
    {
        if (!isset($_COOKIE[$conf['var']['sessionCap' . $type]][$id]))
        {
            $value    = 1;
            $setBlock = true;
        }
        else if ($_COOKIE[$conf['var']['sessionCap' . $type]][$id] >= $sessionCap)
        {
            $value    = -$_COOKIE[$conf['var']['sessionCap' . $type]][$id] + 1;
            $setBlock = true;
        }
        else
        {
            $value = 1;
        }
        MAX_cookieAdd("_{$conf['var']['sessionCap' . $type]}[{$id}]", $value, 0);
    }
    if ($block > 0 || $setBlock)
    {
        MAX_cookieAdd("_{$conf['var']['block' . $type]}[{$id}]", MAX_commonGetTimeNow(), _getTimeThirtyDaysFromNow());
    }
}
function MAX_cookieClientCookieSet($name, $value, $expire, $path = '/', $domain = null)
{
    if (isset($GLOBALS['_OA']['invocationType']) && $GLOBALS['_OA']['invocationType'] == 'xmlrpc')
    {
        if (!isset($GLOBALS['_OA']['COOKIE']['XMLRPC_CACHE']))
        {
            $GLOBALS['_OA']['COOKIE']['XMLRPC_CACHE'] = array();
        }
        $GLOBALS['_OA']['COOKIE']['XMLRPC_CACHE'][$name] = array(
            $value,
            $expire
        );
    }
    else
    {
        @setcookie($name, $value, $expire, $path, $domain);
    }
}
function MAX_cookieClientCookieUnset($name)
{
    $conf   = $GLOBALS['_MAX']['CONF'];
    $domain = (!empty($conf['cookie']['domain'])) ? $conf['cookie']['domain'] : null;
    MAX_cookieSet($name, false, _getTimeYearAgo(), '/', $domain);
    MAX_cookieSet(str_replace('_', '%5F', urlencode($name)), false, _getTimeYearAgo(), '/', $domain);
}
function MAX_cookieClientCookieFlush()
{
    $conf = $GLOBALS['_MAX']['CONF'];
    MAX_cookieSendP3PHeaders();
    if (!empty($GLOBALS['_MAX']['COOKIE']['CACHE']))
    {
        reset($GLOBALS['_MAX']['COOKIE']['CACHE']);
        while (list($name, $v) = each($GLOBALS['_MAX']['COOKIE']['CACHE']))
        {
            list($value, $expire) = $v;
            if ($name == $conf['var']['viewerId'])
            {
                MAX_cookieClientCookieSet($name, $value, $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
            }
            else
            {
                MAX_cookieSet($name, $value, $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
            }
        }
        $GLOBALS['_MAX']['COOKIE']['CACHE'] = array();
    }
    $cookieNames = $GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'];
    if (!is_array($cookieNames))
        return;
    $maxCookieSize = !empty($conf['cookie']['maxCookieSize']) ? $conf['cookie']['maxCookieSize'] : 2048;
    foreach ($cookieNames as $cookieName)
    {
        if (empty($_COOKIE["_{$cookieName}"]))
        {
            continue;
        }
        switch ($cookieName)
        {
            case $conf['var']['blockAd']:
            case $conf['var']['blockCampaign']:
            case $conf['var']['blockZone']:
                $expire = _getTimeThirtyDaysFromNow();
                break;
            case $conf['var']['lastClick']:
            case $conf['var']['lastView']:
            case $conf['var']['capAd']:
            case $conf['var']['capCampaign']:
            case $conf['var']['capZone']:
                $expire = _getTimeYearFromNow();
                break;
            case $conf['var']['sessionCapCampaign']:
            case $conf['var']['sessionCapAd']:
            case $conf['var']['sessionCapZone']:
                $expire = 0;
                break;
        }
        if (!empty($_COOKIE[$cookieName]) && is_array($_COOKIE[$cookieName]))
        {
            $data = array();
            foreach ($_COOKIE[$cookieName] as $adId => $value)
            {
                $data[] = "{$adId}.{$value}";
            }
            while (strlen(implode('_', $data)) > $maxCookieSize)
            {
                $data = array_slice($data, 1);
            }
            MAX_cookieSet($cookieName, implode('_', $data), $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
        }
    }
}
function MAX_cookieSendP3PHeaders()
{
    if ($GLOBALS['_MAX']['CONF']['p3p']['policies'])
    {
        MAX_header("P3P: " . _generateP3PHeader());
    }
}
function _generateP3PHeader()
{
    $conf       = $GLOBALS['_MAX']['CONF'];
    $p3p_header = '';
    if ($conf['p3p']['policies'])
    {
        if ($conf['p3p']['policyLocation'] != '')
        {
            $p3p_header .= " policyref=\"" . $conf['p3p']['policyLocation'] . "\"";
        }
        if ($conf['p3p']['policyLocation'] != '' && $conf['p3p']['compactPolicy'] != '')
        {
            $p3p_header .= ", ";
        }
        if ($conf['p3p']['compactPolicy'] != '')
        {
            $p3p_header .= " CP=\"" . $conf['p3p']['compactPolicy'] . "\"";
        }
    }
    return $p3p_header;
}


$file                            = '/lib/max/Delivery/remotehost.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
function MAX_remotehostSetInfo($run = false)
{
    if (empty($GLOBALS['_OA']['invocationType']) || $run || ($GLOBALS['_OA']['invocationType'] != 'xmlrpc'))
    {
        MAX_remotehostProxyLookup();
        MAX_remotehostReverseLookup();
        MAX_remotehostSetGeoInfo();
    }
}
function MAX_remotehostProxyLookup()
{
    $conf = $GLOBALS['_MAX']['CONF'];
    if ($conf['logging']['proxyLookup'])
    {
        OX_Delivery_logMessage('checking remote host proxy', 7);
        $proxy = false;
        if (!empty($_SERVER['HTTP_VIA']) || !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $proxy = true;
        }
        elseif (!empty($_SERVER['REMOTE_HOST']))
        {
            $aProxyHosts = array(
                'proxy',
                'cache',
                'inktomi'
            );
            foreach ($aProxyHosts as $proxyName)
            {
                if (strpos($_SERVER['REMOTE_HOST'], $proxyName) !== false)
                {
                    $proxy = true;
                    break;
                }
            }
        }
        if ($proxy)
        {
            OX_Delivery_logMessage('proxy detected', 7);
            $aHeaders = array(
                'HTTP_FORWARDED',
                'HTTP_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_CLIENT_IP'
            );
            foreach ($aHeaders as $header)
            {
                if (!empty($_SERVER[$header]))
                {
                    $ip = $_SERVER[$header];
                    break;
                }
            }
            if (!empty($ip))
            {
                foreach (explode(',', $ip) as $ip)
                {
                    $ip = trim($ip);
                    if (($ip != 'unknown') && (!MAX_remotehostPrivateAddress($ip)))
                    {
                        $_SERVER['REMOTE_ADDR'] = $ip;
                        $_SERVER['REMOTE_HOST'] = '';
                        $_SERVER['HTTP_VIA']    = '';
                        OX_Delivery_logMessage('real address set to ' . $ip, 7);
                        break;
                    }
                }
            }
        }
    }
}
function MAX_remotehostReverseLookup()
{
    if (empty($_SERVER['REMOTE_HOST']))
    {
        if ($GLOBALS['_MAX']['CONF']['logging']['reverseLookup'])
        {
            $_SERVER['REMOTE_HOST'] = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
        }
        else
        {
            $_SERVER['REMOTE_HOST'] = $_SERVER['REMOTE_ADDR'];
        }
    }
}
function MAX_remotehostSetGeoInfo()
{
    if (!function_exists('parseDeliveryIniFile'))
    {
        
    }
    $aConf = $GLOBALS['_MAX']['CONF'];
    $type  = (!empty($aConf['geotargeting']['type'])) ? $aConf['geotargeting']['type'] : null;
    if (!is_null($type) && $type != 'none')
    {
        $aComponent = explode(':', $aConf['geotargeting']['type']);
        if (!empty($aComponent[1]) && (!empty($aConf['pluginGroupComponents'][$aComponent[1]])))
        {
            $GLOBALS['_MAX']['CLIENT_GEO'] = OX_Delivery_Common_hook('getGeoInfo', array(), $type);
        }
    }
}
function MAX_remotehostPrivateAddress($ip)
{
    $ip = ip2long($ip);
    if (!$ip)
        return false;
    return (MAX_remotehostMatchSubnet($ip, '10.0.0.0', 8) || MAX_remotehostMatchSubnet($ip, '172.16.0.0', 12) || MAX_remotehostMatchSubnet($ip, '192.168.0.0', 16) || MAX_remotehostMatchSubnet($ip, '127.0.0.0', 8));
}
function MAX_remotehostMatchSubnet($ip, $net, $mask)
{
    $net = ip2long($net);
    if (!is_integer($ip))
    {
        $ip = ip2long($ip);
    }
    if (!$ip || !$net)
    {
        return false;
    }
    if (is_integer($mask))
    {
        if ($mask > 32 || $mask <= 0)
            return false;
        elseif ($mask == 32)
            $mask = ~0;
        else
            $mask = ~((1 << (32 - $mask)) - 1);
    }
    elseif (!($mask = ip2long($mask)))
    {
        return false;
    }
    return ($ip & $mask) == ($net & $mask) ? true : false;
}
function OX_Delivery_Common_hook($hookName, $aParams = array(), $functionName = '')
{
    $return = null;
    if (!empty($functionName))
    {
        $aParts = explode(':', $functionName);
        if (count($aParts) === 3)
        {
            $functionName = OX_Delivery_Common_getFunctionFromComponentIdentifier($functionName, $hookName);
        }
        if (function_exists($functionName))
        {
            $return = call_user_func_array($functionName, $aParams);
        }
    }
    else
    {
        if (!empty($GLOBALS['_MAX']['CONF']['deliveryHooks'][$hookName]))
        {
            $return = array();
            $hooks  = explode('|', $GLOBALS['_MAX']['CONF']['deliveryHooks'][$hookName]);
            print_r($hooks);
            foreach ($hooks as $identifier)
            {
                $functionName = OX_Delivery_Common_getFunctionFromComponentIdentifier($identifier, $hookName);
                if (function_exists($functionName))
                {
                    OX_Delivery_logMessage('calling on ' . $functionName, 7);
                    $return[$identifier] = call_user_func_array($functionName, $aParams);
                }
            }
        }
    }
    return $return;
}
function OX_Delivery_Common_getFunctionFromComponentIdentifier($identifier, $hook = null)
{
    $aInfo        = explode(':', $identifier);
    $functionName = 'Plugin_' . implode('_', $aInfo) . '_Delivery' . (!empty($hook) ? '_' . $hook : '');
    if (!function_exists($functionName))
    {
        if (!empty($GLOBALS['_MAX']['CONF']['pluginSettings']['useMergedFunctions']))
            _includeDeliveryPluginFile('/var/cache/' . OX_getHostName() . '_mergedDeliveryFunctions.php');
        if (!function_exists($functionName))
        {
            _includeDeliveryPluginFile($GLOBALS['_MAX']['CONF']['pluginPaths']['plugins'] . '/' . implode('/', $aInfo) . '.delivery.php');
            if (!function_exists($functionName))
            {
                _includeDeliveryPluginFile('/lib/OX/Extension/' . $aInfo[0] . '/' . $aInfo[0] . 'Delivery.php');
                $functionName = 'Plugin_' . $aInfo[0] . '_delivery';
                if (!empty($hook) && function_exists($functionName . '_' . $hook))
                {
                    $functionName .= '_' . $hook;
                }
            }
        }
    }
    return $functionName;
}
function _includeDeliveryPluginFile($fileName)
{
    if (!in_array($fileName, array_keys($GLOBALS['_MAX']['FILES'])))
    {
        $GLOBALS['_MAX']['FILES'][$fileName] = true;
        if (file_exists(MAX_PATH . $fileName))
        {
            include MAX_PATH . $fileName;
        }
    }
}
function OX_Delivery_logMessage($message, $priority = 6)
{
    $conf = $GLOBALS['_MAX']['CONF'];
    if (empty($conf['googleLog']['enabled']))
        return true;
    $priorityLevel = is_numeric($conf['googleLog']['priority']) ? $conf['googleLog']['priority'] : 6;
    if ($priority > $priorityLevel && empty($_REQUEST[$conf['var']['trace']]))
    {
        return true;
    }
    error_log('[' . date('r') . "] {$conf['log']['ident']}-google-{$GLOBALS['_MAX']['thread_id']}: {$message}\n", 3, MAX_PATH . '/var/' . $conf['googleLog']['name']);
    return true;
}
function MAX_Dal_Delivery_Include()
{
    static $included;
    if (isset($included))
    {
        return;
    }
    $included = true;
    $conf     = $GLOBALS['_MAX']['CONF'];
    if (isset($conf['origin']['type']) && is_readable(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php'))
    {
        require(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php');
    }
    else
    {
        require(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['database']['type']) . '.php');
    }
}
