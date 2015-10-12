<?php


function parseDeliveryIniFile($configPath = null, $configFile = null, $sections = true)//////////7
{

		if (!$configPath) {
		$configPath = MAX_PATH . '/var';
		}
		if ($configFile) {
		$configFile = '.' . $configFile;
		}
		$host = OX_getHostName();
		$configFileName = $configPath . '/' . $host . $configFile . '.conf.php';
		$conf = @parse_ini_file($configFileName, $sections);
		if (isset($conf['realConfig'])) {
		$realconf = @parse_ini_file(MAX_PATH . '/var/' . $conf['realConfig'] . '.conf.php', $sections);
		$conf = mergeConfigFiles($realconf, $conf);
		}
		if (!empty($conf)) {
		return $conf;
		} elseif ($configFile === '.plugin') {
		$pluginType = basename($configPath);
		$defaultConfig = MAX_PATH . '/plugins/' . $pluginType . '/default.plugin.conf.php';
		$conf = @parse_ini_file($defaultConfig, $sections);
		if ($conf !== false) {
		return $conf;
		}
		echo "OpenX could not read the default configuration file for the {$pluginType} plugin";
		exit(1);
		}
		$configFileName = $configPath . '/default' . $configFile . '.conf.php';
		$conf = @parse_ini_file($configFileName, $sections);
		if (isset($conf['realConfig'])) {
		$conf = @parse_ini_file(MAX_PATH . '/var/' . $conf['realConfig'] . '.conf.php', $sections);
		}
		if (!empty($conf)) {
		return $conf;
		}
		if (file_exists(MAX_PATH . '/var/INSTALLED')) {
		echo "OpenX has been installed, but no configuration file was found.\n";
		exit(1);
		}
		echo "OpenX has not been installed yet -- please read the INSTALL.txt file.\n";
		exit(1);
}
if (!function_exists('mergeConfigFiles')) /////////1
{

		function mergeConfigFiles($realConfig, $fakeConfig)
		{
		foreach ($fakeConfig as $key => $value) {
		if (is_array($value)) {
		if (!isset($realConfig[$key])) {
		$realConfig[$key] = array();
		}
		$realConfig[$key] = mergeConfigFiles($realConfig[$key], $value);
		} else {
		if (isset($realConfig[$key]) && is_array($realConfig[$key])) {
		$realConfig[$key][0] = $value;
		} else {
		if (isset($realConfig) && !is_array($realConfig)) {
		$temp = $realConfig;
		$realConfig = array();
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


function OX_getMinimumRequiredMemory($limit = null)///////2
{
		
		if ($limit == 'maintenance')
		{
		return 134217728; 
		}
		return 134217728; 
}
function OX_getMemoryLimitSizeInBytes()/////4
{
		$phpMemoryLimit = ini_get('memory_limit');
		if (empty($phpMemoryLimit) || $phpMemoryLimit == -1)
		{
		return -1;
		}
		$aSize = array(
		'G' => 1073741824,
		'M' => 1048576,
		'K' => 1024
		);
		$phpMemoryLimitInBytes = $phpMemoryLimit;
		foreach($aSize as $type => $multiplier)
		{
		$pos = strpos($phpMemoryLimit, $type);
		if (!$pos) {
		$pos = strpos($phpMemoryLimit, strtolower($type));
		}
		if ($pos) {
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
		$memoryCanBeSet = ($phpMemoryLimitInBytes != $newPhpMemoryLimitInBytes);
		@ini_set('memory_limit', $phpMemoryLimitInBytes);
		return $memoryCanBeSet;
}
function OX_increaseMemoryLimit($setMemory)/////////3
{
		
		$phpMemoryLimitInBytes = OX_getMemoryLimitSizeInBytes();
		if ($phpMemoryLimitInBytes == -1) {
		return true;
		}
		if ($setMemory > $phpMemoryLimitInBytes) {
		if (@ini_set('memory_limit', $setMemory) === false) {
		return false;
		}
		}
		return true;
}
function setupConfigVariables()///////////9
{
		
		$GLOBALS['_MAX']['MAX_DELIVERY_MULTIPLE_DELIMITER'] = '|';
		$GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX'] = '__';
		$GLOBALS['_MAX']['thread_id'] = uniqid();
		$GLOBALS['_MAX']['SSL_REQUEST'] = false;
		if (
		(!empty($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == $GLOBALS['_MAX']['CONF']['openads']['sslPort'])) ||
		(!empty($_SERVER['HTTPS']) && ((strtolower($_SERVER['HTTPS']) == 'on') || ($_SERVER['HTTPS'] == 1))) ||
		(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ||
		(!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on')) ||
		(!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && (strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) == 'on')) ||
		(!empty($_SERVER['FRONT-END-HTTPS']) && (strtolower($_SERVER['FRONT-END-HTTPS']) == 'on'))
		) {
		$GLOBALS['_MAX']['SSL_REQUEST'] = true;
		}
		$GLOBALS['_MAX']['MAX_RAND'] = isset($GLOBALS['_MAX']['CONF']['priority']['randmax']) ?
		$GLOBALS['_MAX']['CONF']['priority']['randmax'] : 2147483647;
		list($micro_seconds, $seconds) = explode(" ", microtime());
		$GLOBALS['_MAX']['NOW_ms'] = round(1000 *((float)$micro_seconds + (float)$seconds));
		if (substr($_SERVER['SCRIPT_NAME'], -11) != 'install.php') {
		$GLOBALS['serverTimezone'] = date_default_timezone_get();
		OA_setTimeZoneUTC();
		}
}
function setupServerVariables()//////////////5
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
function setupDeliveryConfigVariables()///////6
{
		
		if (!defined('MAX_PATH')) {
		define('MAX_PATH', dirname(__FILE__).'/../..');
		}
		if (!defined('OX_PATH')) {
		define('OX_PATH', MAX_PATH);
		}
		if (!defined('LIB_PATH')) {
		define('LIB_PATH', MAX_PATH. DIRECTORY_SEPARATOR. 'lib'. DIRECTORY_SEPARATOR. 'OX');
		}
		if ( !(isset($GLOBALS['_MAX']['CONF']))) {
		$GLOBALS['_MAX']['CONF'] = parseDeliveryIniFile();
		}
		setupConfigVariables();
}
function OA_setTimeZone($timezone)///11
{
		date_default_timezone_set($timezone);
		$GLOBALS['_DATE_TIMEZONE_DEFAULT'] = $timezone;
}
function OA_setTimeZoneUTC()////10
{
		OA_setTimeZone('UTC');
}
function OA_setTimeZoneLocal()
{
		$tz = !empty($GLOBALS['_MAX']['PREF']['timezone']) ? $GLOBALS['_MAX']['PREF']['timezone'] : 'GMT';
		OA_setTimeZone($tz);
}
function OX_getHostName()/////////8
{
		if (!empty($_SERVER['HTTP_HOST'])) {
		$host = explode(':', $_SERVER['HTTP_HOST']);
		$host = $host[0];
		} else if (!empty($_SERVER['SERVER_NAME'])) {
		$host = explode(':', $_SERVER['SERVER_NAME']);
		$host = $host[0];
		}
		return $host;
}
function OX_getHostNameWithPort()
{
		if (!empty($_SERVER['HTTP_HOST'])) {
		$host = $_SERVER['HTTP_HOST'];
		} else if (!empty($_SERVER['SERVER_NAME'])) {
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
		$oxPearPath = MAX_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'pear';
		$oxZendPath = MAX_PATH . DIRECTORY_SEPARATOR . 'lib';
		set_include_path($oxPearPath . PATH_SEPARATOR . $oxZendPath . PATH_SEPARATOR . get_include_path());
}

OX_increaseMemoryLimit(OX_getMinimumRequiredMemory());

if (!defined('E_DEPRECATED'))
{
define('E_DEPRECATED', 0);
}
setupServerVariables();
setupDeliveryConfigVariables();
$conf = $GLOBALS['_MAX']['CONF'];
$GLOBALS['_OA']['invocationType'] = array_search(basename($_SERVER['SCRIPT_FILENAME']), $conf['file']);

	if (!empty($conf['debug']['production']))
	{
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
	}
	else
	{
	error_reporting(E_ALL ^ E_DEPRECATED);
	}
$file = '/lib/max/Delivery/common.php';
$GLOBALS['_MAX']['FILES'][$file] = true;

$file = '/lib/max/Delivery/cookie.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
$GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'] = array();

if (!is_callable('MAX_cookieSet')) /////12--Note
{
	if (!empty($conf['cookie']['plugin']) && is_readable(MAX_PATH . "/plugins/cookieStorage/{$conf['cookie']['plugin']}.delivery.php"))
	{
		include MAX_PATH . "/plugins/cookieStorage/{$conf['cookie']['plugin']}.delivery.php";
	}
	else
	{
		function MAX_cookieSet($name, $value, $expire, $path = '/', $domain = null) { return MAX_cookieClientCookieSet($name, $value, $expire, $path, $domain); }
		function MAX_cookieUnset($name) { return MAX_cookieClientCookieUnset($name); }
		function MAX_cookieFlush() { return MAX_cookieClientCookieFlush(); }
		function MAX_cookieLoad() { return true; }
	}
}
function MAX_cookieAdd($name, $value, $expire = 0)
{
		if (!isset($GLOBALS['_MAX']['COOKIE']['CACHE'])) {
		$GLOBALS['_MAX']['COOKIE']['CACHE'] = array();
		}
		$GLOBALS['_MAX']['COOKIE']['CACHE'][$name] = array($value, $expire);
}
function MAX_cookieSetViewerIdAndRedirect($viewerId)
{
		$aConf = $GLOBALS['_MAX']['CONF'];
		MAX_cookieAdd($aConf['var']['viewerId'], $viewerId, _getTimeYearFromNow());
		MAX_cookieFlush();
		if ($GLOBALS['_MAX']['SSL_REQUEST']) {
		$url = MAX_commonConstructSecureDeliveryUrl(basename($_SERVER['SCRIPT_NAME']));
		} else {
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
function _getTimeYearFromNow()//44
{
		return MAX_commonGetTimeNow() + 31536000; 
}
function _getTimeYearAgo()//////42
{
		return MAX_commonGetTimeNow() - 31536000; 
}
function MAX_cookieUnpackCapping()///////13--NOte
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$cookieNames = $GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'];
		if (!is_array($cookieNames))
		return;
		foreach ($cookieNames as $cookieName) {
		if (!empty($_COOKIE[$cookieName])) {
		if (!is_array($_COOKIE[$cookieName])) {
		$output = array();
		$data = explode('_', $_COOKIE[$cookieName]);
		foreach ($data as $pair) {
		list($name, $value) = explode('.', $pair);
		$output[$name] = $value;
		}
		$_COOKIE[$cookieName] = $output;
		}
		}
		if (!empty($_COOKIE['_' . $cookieName]) && is_array($_COOKIE['_' . $cookieName])) {
		foreach ($_COOKIE['_' . $cookieName] as $adId => $cookie) {
		if (_isBlockCookie($cookieName)) {
		$_COOKIE[$cookieName][$adId] = $cookie;
		} else {
		if (isset($_COOKIE[$cookieName][$adId])) {
		$_COOKIE[$cookieName][$adId] += $cookie;
		} else {
		$_COOKIE[$cookieName][$adId] = $cookie;
		}
		}
		MAX_cookieUnset("_{$cookieName}[{$adId}]");
		}
		}
		}
}
function _isBlockCookie($cookieName)///41
{
		return in_array($cookieName, array(
		$GLOBALS['_MAX']['CONF']['var']['blockAd'],
		$GLOBALS['_MAX']['CONF']['var']['blockCampaign'],
		$GLOBALS['_MAX']['CONF']['var']['blockZone'],
		$GLOBALS['_MAX']['CONF']['var']['lastView'],
		$GLOBALS['_MAX']['CONF']['var']['lastClick'],
		$GLOBALS['_MAX']['CONF']['var']['blockLoggingClick'],
		));
}
function MAX_cookieGetUniqueViewerId($create = true)
{
		static $uniqueViewerId = null;
		if(!is_null($uniqueViewerId)) {
		return $uniqueViewerId;
		}
		$conf = $GLOBALS['_MAX']['CONF'];
		if (isset($_COOKIE[$conf['var']['viewerId']])) {
		$uniqueViewerId = $_COOKIE[$conf['var']['viewerId']];
		} elseif ($create) {
		$uniqueViewerId = md5(uniqid('', true));  $GLOBALS['_MAX']['COOKIE']['newViewerId'] = true;
		}
		return $uniqueViewerId;
}
function MAX_cookieGetCookielessViewerID()
{
		if (empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['HTTP_USER_AGENT'])) {
		return '';
		}
		$cookiePrefix = $GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX'];
		return $cookiePrefix . substr(md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']), 0, 32-(strlen($cookiePrefix)));
}
function MAX_Delivery_cookie_cappingOnRequest()
{
		if (isset($GLOBALS['_OA']['invocationType']) &&
		($GLOBALS['_OA']['invocationType'] == 'xmlrpc' || $GLOBALS['_OA']['invocationType'] == 'view')
		) {
		return true;
		}
		return !$GLOBALS['_MAX']['CONF']['logging']['adImpressions'];
}
function MAX_Delivery_cookie_setCapping($type, $id, $block = 0, $cap = 0, $sessionCap = 0)
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$setBlock = false;
		if ($cap > 0) {
		$expire = MAX_commonGetTimeNow() + $conf['cookie']['permCookieSeconds'];
		if (!isset($_COOKIE[$conf['var']['cap' . $type]][$id])) {
		$value = 1;
		$setBlock = true;
		} else if ($_COOKIE[$conf['var']['cap' . $type]][$id] >= $cap) {
		$value = -$_COOKIE[$conf['var']['cap' . $type]][$id]+1;
		$setBlock = true;
		} else {
		$value = 1;
		}
		MAX_cookieAdd("_{$conf['var']['cap' . $type]}[{$id}]", $value, $expire);
		}
		if ($sessionCap > 0) {
		if (!isset($_COOKIE[$conf['var']['sessionCap' . $type]][$id])) {
		$value = 1;
		$setBlock = true;
		} else if ($_COOKIE[$conf['var']['sessionCap' . $type]][$id] >= $sessionCap) {
		$value = -$_COOKIE[$conf['var']['sessionCap' . $type]][$id]+1;
		$setBlock = true;
		} else {
		$value = 1;
		}
		MAX_cookieAdd("_{$conf['var']['sessionCap' . $type]}[{$id}]", $value, 0);
		}
		if ($block > 0 || $setBlock) {
		MAX_cookieAdd("_{$conf['var']['block' . $type]}[{$id}]", MAX_commonGetTimeNow(), _getTimeThirtyDaysFromNow());
		}
}
function MAX_cookieClientCookieSet($name, $value, $expire, $path = '/', $domain = null)///43
{
		 if (isset($GLOBALS['_OA']['invocationType']) && $GLOBALS['_OA']['invocationType'] == 'xmlrpc') {
		if (!isset($GLOBALS['_OA']['COOKIE']['XMLRPC_CACHE'])) {
		$GLOBALS['_OA']['COOKIE']['XMLRPC_CACHE'] = array();
		}
		$GLOBALS['_OA']['COOKIE']['XMLRPC_CACHE'][$name] = array($value, $expire);
		} else {
		@setcookie($name, $value, $expire, $path, $domain);
		}
}
function MAX_cookieClientCookieUnset($name)////42
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$domain = (!empty($conf['cookie']['domain'])) ? $conf['cookie']['domain'] : null;
		MAX_cookieSet($name, false, _getTimeYearAgo(), '/', $domain);
		MAX_cookieSet(str_replace('_', '%5F', urlencode($name)), false, _getTimeYearAgo(), '/', $domain);
}
function MAX_cookieClientCookieFlush()/////14-Note
{
		$conf = $GLOBALS['_MAX']['CONF'];
		MAX_cookieSendP3PHeaders();
		if (!empty($GLOBALS['_MAX']['COOKIE']['CACHE'])) {
		reset($GLOBALS['_MAX']['COOKIE']['CACHE']);
		while (list($name,$v) = each ($GLOBALS['_MAX']['COOKIE']['CACHE'])) {
		list($value, $expire) = $v;
		if ($name == $conf['var']['viewerId']) {
		MAX_cookieClientCookieSet($name, $value, $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
		} else {
		MAX_cookieSet($name, $value, $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
		}
		}
		$GLOBALS['_MAX']['COOKIE']['CACHE'] = array();
		}
		$cookieNames = $GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'];
		if (!is_array($cookieNames))
		return;
		$maxCookieSize = !empty($conf['cookie']['maxCookieSize']) ? $conf['cookie']['maxCookieSize'] : 2048;
		foreach ($cookieNames as $cookieName) {
		if (empty($_COOKIE["_{$cookieName}"])) {
		continue;
		}
		switch ($cookieName) {
		case $conf['var']['blockAd'] :
		case $conf['var']['blockCampaign'] :
		case $conf['var']['blockZone'] : $expire = _getTimeThirtyDaysFromNow(); break;
		case $conf['var']['lastClick'] :
		case $conf['var']['lastView'] :
		case $conf['var']['capAd'] :
		case $conf['var']['capCampaign'] :
		case $conf['var']['capZone'] : $expire = _getTimeYearFromNow(); break;
		case $conf['var']['sessionCapCampaign'] :
		case $conf['var']['sessionCapAd'] :
		case $conf['var']['sessionCapZone'] : $expire = 0; break;
		}
		if (!empty($_COOKIE[$cookieName]) && is_array($_COOKIE[$cookieName])) {
		$data = array();
		foreach ($_COOKIE[$cookieName] as $adId => $value) {
		$data[] = "{$adId}.{$value}";
		}
		while (strlen(implode('_', $data)) > $maxCookieSize) {
		$data = array_slice($data, 1);
		}
		MAX_cookieSet($cookieName, implode('_', $data), $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
		}
		}
}
function MAX_cookieSendP3PHeaders()/////////15-Note
{
		if ($GLOBALS['_MAX']['CONF']['p3p']['policies'])
		{
		MAX_header("P3P: ". _generateP3PHeader());
		}
}
function _generateP3PHeader()//////////16
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$p3p_header = '';
		if ($conf['p3p']['policies']) {
		if ($conf['p3p']['policyLocation'] != '') {
		$p3p_header .= " policyref=\"".$conf['p3p']['policyLocation']."\"";
		}
		if ($conf['p3p']['policyLocation'] != '' && $conf['p3p']['compactPolicy'] != '') {
		$p3p_header .= ", ";
		}
		if ($conf['p3p']['compactPolicy'] != '') {
		$p3p_header .= " CP=\"".$conf['p3p']['compactPolicy']."\"";
		}
		}
		return $p3p_header;
}

$file = '/lib/max/Delivery/remotehost.php';
$GLOBALS['_MAX']['FILES'][$file] = true;

function MAX_remotehostSetInfo($run = false)////17
{
		if (empty($GLOBALS['_OA']['invocationType']) || $run || ($GLOBALS['_OA']['invocationType'] != 'xmlrpc')) {
		MAX_remotehostProxyLookup();
		MAX_remotehostReverseLookup();
		MAX_remotehostSetGeoInfo();
		}
}
function MAX_remotehostProxyLookup()///////18
{
		$conf = $GLOBALS['_MAX']['CONF'];
		if ($conf['logging']['proxyLookup']) {
		OX_Delivery_logMessage('checking remote host proxy', 7);
		$proxy = false;
		if (!empty($_SERVER['HTTP_VIA']) || !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$proxy = true;
		} elseif (!empty($_SERVER['REMOTE_HOST'])) {
		$aProxyHosts = array(
		'proxy',
		'cache',
		'inktomi'
		);
		foreach ($aProxyHosts as $proxyName) {
		if (strpos($_SERVER['REMOTE_HOST'], $proxyName) !== false) {
		$proxy = true;
		break;
		}
		}
		}
		if ($proxy) {
		OX_Delivery_logMessage('proxy detected', 7);
		$aHeaders = array(
		'HTTP_FORWARDED',
		'HTTP_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_CLIENT_IP'
		);
		foreach ($aHeaders as $header) {
		if (!empty($_SERVER[$header])) {
		$ip = $_SERVER[$header];
		break;
		}
		}
		if (!empty($ip)) {
		foreach (explode(',', $ip) as $ip) {
		$ip = trim($ip);
		if (($ip != 'unknown') && (!MAX_remotehostPrivateAddress($ip))) {
		$_SERVER['REMOTE_ADDR'] = $ip;
		$_SERVER['REMOTE_HOST'] = '';
		$_SERVER['HTTP_VIA'] = '';
		OX_Delivery_logMessage('real address set to '.$ip, 7);
		break;
		}
		}
		}
		}
		}
}
function MAX_remotehostReverseLookup()/////19
{
		if (empty($_SERVER['REMOTE_HOST'])) {
		if ($GLOBALS['_MAX']['CONF']['logging']['reverseLookup']) {
		$_SERVER['REMOTE_HOST'] = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
		} else {
		$_SERVER['REMOTE_HOST'] = $_SERVER['REMOTE_ADDR'];
		}
		}
}
function MAX_remotehostSetGeoInfo()////////20
{
		if (!function_exists('parseDeliveryIniFile')) {

		}
		$aConf = $GLOBALS['_MAX']['CONF'];
		$type = (!empty($aConf['geotargeting']['type'])) ? $aConf['geotargeting']['type'] : null;
		if (!is_null($type) && $type != 'none') {
		$aComponent = explode(':', $aConf['geotargeting']['type']);
		if (!empty($aComponent[1]) && (!empty($aConf['pluginGroupComponents'][$aComponent[1]]))) {
		$GLOBALS['_MAX']['CLIENT_GEO'] = OX_Delivery_Common_hook('getGeoInfo', array(), $type);
		}
		}
}
function MAX_remotehostPrivateAddress($ip)
{
		setupIncludePath();
		require_once 'Net/IPv4.php';

		$aPrivateNetworks = array(
		'10.0.0.0/8',
		'172.16.0.0/12',
		'192.168.0.0/16',
		'127.0.0.0/24'
		);
		foreach ($aPrivateNetworks as $privateNetwork) {
		if (Net_IPv4::ipInNetwork($ip, $privateNetwork)) {
		return true;
		}
		}
		return false;
}


$file = '/lib/max/Delivery/log.php';
$GLOBALS['_MAX']['FILES'][$file] = true;

$file = '/lib/max/Dal/Delivery.php';
$GLOBALS['_MAX']['FILES'][$file] = true;

$file = '/lib/OA/Dal/Delivery.php';
$GLOBALS['_MAX']['FILES'][$file] = true;

function OA_Dal_Delivery_getAccountTZs()
{
		$aConf = $GLOBALS['_MAX']['CONF'];
		$query = "
			SELECT
			    value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['application_variable'])."
			WHERE
			    name = 'admin_account_id'
		    ";
		$res = OA_Dal_Delivery_query($query);
		if (is_resource($res) && OA_Dal_Delivery_numRows($res)) {
		$adminAccountId = (int)OA_Dal_Delivery_result($res, 0, 0);
		} else {
		$adminAccountId = false;
		}
		$query = "
			SELECT
			    a.account_id AS account_id,
			    apa.value AS timezone
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['accounts'])." AS a JOIN
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa ON (apa.account_id = a.account_id) JOIN
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['preferences'])." AS p ON (p.preference_id = apa.preference_id)
			WHERE
			    a.account_type IN ('ADMIN', 'MANAGER') AND
			    p.preference_name = 'timezone'
		    ";
		$res = OA_Dal_Delivery_query($query);
		$aResult = array(
		'adminAccountId' => $adminAccountId,
		'aAccounts' => array()
		);
		if (is_resource($res)) {
		while ($row = OA_Dal_Delivery_fetchAssoc($res)) {
		$accountId = (int)$row['account_id'];
		if ($accountId === $adminAccountId) {
		$aResult['default'] = $row['timezone'];
		} else {
		$aResult['aAccounts'][$accountId] = $row['timezone'];
		}
		}
		}
		if (empty($aResult['default'])) {
		$aResult['default'] = 'UTC';
		}
		return $aResult;
}
function OA_Dal_Delivery_getZoneInfo($zoneid)
{
		$aConf = $GLOBALS['_MAX']['CONF'];
		$zoneid = (int)$zoneid;
		$query = "
			SELECT
			    z.zoneid AS zone_id,
			    z.zonename AS name,
			    z.delivery AS type,
			    z.description AS description,
			    z.width AS width,
			    z.height AS height,
			    z.chain AS chain,
			    z.prepend AS prepend,
			    z.append AS append,
			    z.appendtype AS appendtype,
			    z.forceappend AS forceappend,
			    z.inventory_forecast_type AS inventory_forecast_type,
			    z.block AS block_zone,
			    z.capping AS cap_zone,
			    z.session_capping AS session_cap_zone,
			    z.show_capped_no_cookie AS show_capped_no_cookie_zone,
			    z.ext_adselection AS ext_adselection,
			    z.affiliateid AS publisher_id,
			    a.agencyid AS agency_id,
			    a.account_id AS trafficker_account_id,
			    m.account_id AS manager_account_id
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['zones'])." AS z,
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['affiliates'])." AS a,
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['agency'])." AS m
			WHERE
			    z.zoneid = {$zoneid}
			  AND
			    z.affiliateid = a.affiliateid
			  AND
			    a.agencyid = m.agencyid";
		$rZoneInfo = OA_Dal_Delivery_query($query);
		if (!is_resource($rZoneInfo)) {
		return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : false;
		}
		$aZoneInfo = OA_Dal_Delivery_fetchAssoc($rZoneInfo);
		$query = "
			SELECT
			    p.preference_id AS preference_id,
			    p.preference_name AS preference_name
			FROM
			    {$aConf['table']['prefix']}{$aConf['table']['preferences']} AS p
			WHERE
			    p.preference_name = 'default_banner_image_url'
			    OR
			    p.preference_name = 'default_banner_destination_url'";
		$rPreferenceInfo = OA_Dal_Delivery_query($query);
		if (!is_resource($rPreferenceInfo)) {
		return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : false;
		}
		if (OA_Dal_Delivery_numRows($rPreferenceInfo) != 2) {
		return $aZoneInfo;
		}
		$aPreferenceInfo = OA_Dal_Delivery_fetchAssoc($rPreferenceInfo);
		$variableName = $aPreferenceInfo['preference_name'] . '_id';
		$$variableName = $aPreferenceInfo['preference_id'];
		$aPreferenceInfo = OA_Dal_Delivery_fetchAssoc($rPreferenceInfo);
		$variableName = $aPreferenceInfo['preference_name'] . '_id';
		$$variableName = $aPreferenceInfo['preference_id'];
		$query = "
			SELECT
			    'default_banner_destination_url_trafficker' AS item,
			    apa.value AS value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa
			WHERE
			    apa.account_id = {$aZoneInfo['trafficker_account_id']}
			    AND
			    apa.preference_id = $default_banner_destination_url_id
			UNION
			SELECT
			    'default_banner_destination_url_manager' AS item,
			    apa.value AS value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa
			WHERE
			    apa.account_id = {$aZoneInfo['manager_account_id']}
			    AND
			    apa.preference_id = $default_banner_destination_url_id
			UNION
			SELECT
			    'default_banner_image_url_trafficker' AS item,
			    apa.value AS value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa
			WHERE
			    apa.account_id = {$aZoneInfo['trafficker_account_id']}
			    AND
			    apa.preference_id = $default_banner_image_url_id
			UNION
			SELECT
			    'default_banner_image_url_manager' AS item,
			    apa.value AS value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa
			WHERE
			    apa.account_id = {$aZoneInfo['manager_account_id']}
			    AND
			    apa.preference_id = $default_banner_image_url_id
			UNION
			SELECT
			    'default_banner_image_url_admin' AS item,
			    apa.value AS value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa,
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['accounts'])." AS a
			WHERE
			    apa.account_id = a.account_id
			    AND
			    a.account_type = 'ADMIN'
			    AND
			    apa.preference_id = $default_banner_image_url_id
			UNION
			SELECT
			    'default_banner_destination_url_admin' AS item,
			    apa.value AS value
			FROM
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['account_preference_assoc'])." AS apa,
			    ".OX_escapeIdentifier($aConf['table']['prefix'].$aConf['table']['accounts'])." AS a
			WHERE
			    apa.account_id = a.account_id
			    AND
			    a.account_type = 'ADMIN'
			    AND
			    apa.preference_id = $default_banner_destination_url_id";
		$rDefaultBannerInfo = OA_Dal_Delivery_query($query);
		if (!is_resource($rDefaultBannerInfo)) {
		return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : false;
		}
		if (OA_Dal_Delivery_numRows($rDefaultBannerInfo) == 0) {
		if ($aConf['defaultBanner']['imageUrl'] != '') {
		$aZoneInfo['default_banner_image_url'] = $aConf['defaultBanner']['imageUrl'];
		}
		return $aZoneInfo;
		}
		$aDefaultImageURLs = array();
		$aDefaultDestinationURLs = array();
		while ($aRow = OA_Dal_Delivery_fetchAssoc($rDefaultBannerInfo)) {
		if (stristr($aRow['item'], 'default_banner_image_url')) {
		$aDefaultImageURLs[$aRow['item']] = $aRow['value'];
		} else if (stristr($aRow['item'], 'default_banner_destination_url')) {
		$aDefaultDestinationURLs[$aRow['item']] = $aRow['value'];
		}
		}
		$aTypes = array(
		0 => 'admin',
		1 => 'manager',
		2 => 'trafficker'
		);
		foreach ($aTypes as $type) {
		if (isset($aDefaultImageURLs['default_banner_image_url_' . $type])) {
		$aZoneInfo['default_banner_image_url'] = $aDefaultImageURLs['default_banner_image_url_' . $type];
		}
		if (isset($aDefaultDestinationURLs['default_banner_destination_url_' . $type])) {
		$aZoneInfo['default_banner_destination_url'] = $aDefaultDestinationURLs['default_banner_destination_url_' . $type];
		}
		}
		return $aZoneInfo;
}
function OA_Dal_Delivery_getPublisherZones($publisherid)
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$publisherid = (int)$publisherid;
		$rZones = OA_Dal_Delivery_query("SELECT
			z.zoneid AS zone_id,
			z.affiliateid AS publisher_id,
			z.zonename AS name,
			z.delivery AS type
		    FROM
			".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['zones'])." AS z
		    WHERE
			z.affiliateid={$publisherid}
		    ");

			if (!is_resource($rZones))
			{
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : false;
			}
			while ($aZone = OA_Dal_Delivery_fetchAssoc($rZones))
			{
			$aZones[$aZone['zone_id']] = $aZone;
			}
			return ($aZones);
}
function OA_Dal_Delivery_getZoneLinkedAds($zoneid)
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$zoneid = (int)$zoneid;
		$aRows = OA_Dal_Delivery_getZoneInfo($zoneid);
		$aRows['xAds'] = array();
		$aRows['ads'] = array();
		$aRows['lAds'] = array();
		$aRows['eAds'] = array();
		$aRows['count_active'] = 0;
		$aRows['zone_companion'] = false;
		$aRows['count_active'] = 0;
		$totals = array(
		'xAds' => 0,
		'ads' => 0,
		'lAds' => 0
		);
		$query = "
			SELECT
			    d.bannerid AS ad_id,
			    d.campaignid AS placement_id,
			    d.status AS status,
			    d.description AS name,
			    d.storagetype AS type,
			    d.contenttype AS contenttype,
			    d.pluginversion AS pluginversion,
			    d.filename AS filename,
			    d.imageurl AS imageurl,
			    d.htmltemplate AS htmltemplate,
			    d.htmlcache AS htmlcache,
			    d.width AS width,
			    d.height AS height,
			    d.weight AS weight,
			    d.seq AS seq,
			    d.target AS target,
			    d.url AS url,
			    d.alt AS alt,
			    d.statustext AS statustext,
			    d.bannertext AS bannertext,
			    d.adserver AS adserver,
			    d.block AS block_ad,
			    d.capping AS cap_ad,
			    d.session_capping AS session_cap_ad,
			    d.compiledlimitation AS compiledlimitation,
			    d.acl_plugins AS acl_plugins,
			    d.prepend AS prepend,
			    d.append AS append,
			    d.bannertype AS bannertype,
			    d.alt_filename AS alt_filename,
			    d.alt_imageurl AS alt_imageurl,
			    d.alt_contenttype AS alt_contenttype,
			    d.parameters AS parameters,
			    d.transparent AS transparent,
			    d.ext_bannertype AS ext_bannertype,
			    az.priority AS priority,
			    az.priority_factor AS priority_factor,
			    az.to_be_delivered AS to_be_delivered,
			  c.campaignid AS campaign_id,
			    c.priority AS campaign_priority,
			    c.weight AS campaign_weight,
			    c.companion AS campaign_companion,
			    c.block AS block_campaign,
			    c.capping AS cap_campaign,
			    c.session_capping AS session_cap_campaign,
			    c.show_capped_no_cookie AS show_capped_no_cookie,
			    c.clientid AS client_id,
			    c.expire_time AS expire_time,
			    c.revenue_type AS revenue_type,
			    c.ecpm_enabled AS ecpm_enabled,
			    c.ecpm AS ecpm,
			    c.clickwindow AS clickwindow,
			    c.viewwindow AS viewwindow,
			    m.advertiser_limitation AS advertiser_limitation,
			    a.account_id AS account_id,
			    z.affiliateid AS affiliate_id,
			    a.agencyid as agency_id
			FROM
			    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['banners'])." AS d JOIN
			    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['ad_zone_assoc'])." AS az ON (d.bannerid = az.ad_id) JOIN
			    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['zones'])." AS z ON (az.zone_id = z.zoneid) JOIN
			    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns'])." AS c ON (c.campaignid = d.campaignid) LEFT JOIN
			    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['clients'])." AS m ON (m.clientid = c.clientid) LEFT JOIN
			    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['agency'])." AS a ON (a.agencyid = m.agencyid)
			WHERE
			    az.zone_id = {$zoneid}
			  AND
			    d.status <= 0
			  AND
			    c.status <= 0
		    ";
		$rAds = OA_Dal_Delivery_query($query);
		if (!is_resource($rAds)) {
		return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
		}
		$aConversionLinkedCreatives = MAX_cacheGetTrackerLinkedCreatives();
		while ($aAd = OA_Dal_Delivery_fetchAssoc($rAds)) {
		$aAd['tracker_status'] = (!empty($aConversionLinkedCreatives[$aAd['ad_id']]['status'])) ? $aConversionLinkedCreatives[$aAd['ad_id']]['status'] : null;
		if ($aAd['campaign_priority'] == -1) {
		$aRows['xAds'][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		} elseif ($aAd['campaign_priority'] == 0) {
		$aRows['lAds'][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		} elseif ($aAd['campaign_priority'] == -2) {
		$aRows['eAds'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		} else {
		$aRows['ads'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		}
		if ($aAd['campaign_companion'] == 1) {
		$aRows['zone_companion'][] = $aAd['placement_id'];
		}
		}
		if (is_array($aRows['xAds'])) {
		$totals['xAds'] = _setPriorityFromWeights($aRows['xAds']);
		}
		if (is_array($aRows['ads'])) {
		$totals['ads'] = _getTotalPrioritiesByCP($aRows['ads']);
		}
		if (is_array($aRows['eAds'])) {
		$totals['eAds'] = _getTotalPrioritiesByCP($aRows['eAds']);
		}
		if (is_array($aRows['lAds'])) {
		$totals['lAds'] = _setPriorityFromWeights($aRows['lAds']);
		}
		$aRows['priority'] = $totals;
		return $aRows;
}
function OA_Dal_Delivery_getZoneLinkedAdInfos($zoneid)
{
		$conf = $GLOBALS['_MAX']['CONF'];
		$zoneid = (int)$zoneid;
		$aRows['xAds'] = array();
		$aRows['ads'] = array();
		$aRows['lAds'] = array();
		$aRows['eAds'] = array();
		$aRows['zone_companion'] = false;
		$aRows['count_active'] = 0;
		$query =
		"SELECT "
		."d.bannerid AS ad_id, "  ."d.campaignid AS placement_id, "  ."d.status AS status, "  ."d.width AS width, "
		."d.ext_bannertype AS ext_bannertype, "
		."d.height AS height, "
		."d.storagetype AS type, "  ."d.contenttype AS contenttype, "  ."d.weight AS weight, "  ."d.adserver AS adserver, "  ."d.block AS block_ad, "  ."d.capping AS cap_ad, "  ."d.session_capping AS session_cap_ad, "  ."d.compiledlimitation AS compiledlimitation, "  ."d.acl_plugins AS acl_plugins, "  ."d.alt_filename AS alt_filename, "  ."az.priority AS priority, "  ."az.priority_factor AS priority_factor, "  ."az.to_be_delivered AS to_be_delivered, "  ."c.campaignid AS campaign_id, "  ."c.priority AS campaign_priority, "  ."c.weight AS campaign_weight, "  ."c.companion AS campaign_companion, "  ."c.block AS block_campaign, "  ."c.capping AS cap_campaign, "  ."c.session_capping AS session_cap_campaign, " ."c.show_capped_no_cookie AS show_capped_no_cookie, "
		."c.clientid AS client_id, "  ."c.expire_time AS expire_time, "
		."c.revenue_type AS revenue_type, "
		."c.ecpm_enabled AS ecpm_enabled, "
		."c.ecpm AS ecpm, "
		."ct.status AS tracker_status, "
		.OX_Dal_Delivery_regex("d.htmlcache", "src\\s?=\\s?[\\'\"]http:")." AS html_ssl_unsafe, "
		.OX_Dal_Delivery_regex("d.imageurl", "^http:")." AS url_ssl_unsafe "
		."FROM "
		.OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['banners'])." AS d JOIN "
		.OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['ad_zone_assoc'])." AS az ON (d.bannerid = az.ad_id) JOIN "
		.OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns'])." AS c ON (c.campaignid = d.campaignid) LEFT JOIN "
		.OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns_trackers'])." AS ct ON (ct.campaignid = c.campaignid) "
		."WHERE "
		."az.zone_id = {$zoneid} "
		."AND "
		."d.status <= 0 "
		."AND "
		."c.status <= 0 ";
		$rAds = OA_Dal_Delivery_query($query);
		if (!is_resource($rAds)) {
		return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
		}
		while ($aAd = OA_Dal_Delivery_fetchAssoc($rAds)) {
		if ($aAd['campaign_priority'] == -1) {
		$aRows['xAds'][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		} elseif ($aAd['campaign_priority'] == 0) {
		$aRows['lAds'][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		} elseif ($aAd['campaign_priority'] == -2) {
		$aRows['eAds'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		} else {
		$aRows['ads'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
		$aRows['count_active']++;
		}
		if ($aAd['campaign_companion'] == 1) {
		$aRows['zone_companion'][] = $aAd['placement_id'];  }
		}
		return $aRows;
}
function OA_Dal_Delivery_getLinkedAdInfos($search, $campaignid = '', $lastpart = true)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$campaignid = (int)$campaignid;
			if ($campaignid > 0) {
			$precondition = " AND d.campaignid = '".$campaignid."' ";
			} else {
			$precondition = '';
			}
			$aRows['xAds'] = array();
			$aRows['ads'] = array();
			$aRows['lAds'] = array();
			$aRows['count_active'] = 0;
			$aRows['zone_companion'] = false;
			$aRows['count_active'] = 0;
			$totals = array(
			'xAds' => 0,
			'ads' => 0,
			'lAds' => 0
			);
			$query = OA_Dal_Delivery_buildAdInfoQuery($search, $lastpart, $precondition);
			$rAds = OA_Dal_Delivery_query($query);
			if (!is_resource($rAds)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			}
			while ($aAd = OA_Dal_Delivery_fetchAssoc($rAds)) {
			if ($aAd['campaign_priority'] == -1) {
			$aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
			$aRows['xAds'][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			$totals['xAds'] += $aAd['priority'];
			} elseif ($aAd['campaign_priority'] == 0) {
			$aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
			$aRows['lAds'][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			$totals['lAds'] += $aAd['priority'];
			} elseif ($aAd['campaign_priority'] == -2) {
			$aRows['eAds'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			} else {
			$aRows['ads'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			}
			}
			return $aRows;
			}
			function OA_Dal_Delivery_getLinkedAds($search, $campaignid = '', $lastpart = true) {
			$conf = $GLOBALS['_MAX']['CONF'];
			$campaignid = (int)$campaignid;
			if ($campaignid > 0) {
			$precondition = " AND d.campaignid = '".$campaignid."' ";
			} else {
			$precondition = '';
			}
			$aRows['xAds'] = array();
			$aRows['ads'] = array();
			$aRows['lAds'] = array();
			$aRows['count_active'] = 0;
			$aRows['zone_companion'] = false;
			$aRows['count_active'] = 0;
			$totals = array(
			'xAds' => 0,
			'ads' => 0,
			'lAds' => 0
			);
			$query = OA_Dal_Delivery_buildQuery($search, $lastpart, $precondition);
			$rAds = OA_Dal_Delivery_query($query);
			if (!is_resource($rAds)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			}
			$aConversionLinkedCreatives = MAX_cacheGetTrackerLinkedCreatives();
			while ($aAd = OA_Dal_Delivery_fetchAssoc($rAds)) {
			$aAd['tracker_status'] = (!empty($aConversionLinkedCreatives[$aAd['ad_id']]['status'])) ? $aConversionLinkedCreatives[$aAd['ad_id']]['status'] : null;
			if ($aAd['campaign_priority'] == -1) {
			$aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
			$aRows['xAds'][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			$totals['xAds'] += $aAd['priority'];
			} elseif ($aAd['campaign_priority'] == 0) {
			$aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
			$aRows['lAds'][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			$totals['lAds'] += $aAd['priority'];
			} elseif ($aAd['campaign_priority'] == -2) {
			$aRows['eAds'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			} else {
			$aRows['ads'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
			$aRows['count_active']++;
			}
			}
			if (isset($aRows['xAds']) && is_array($aRows['xAds'])) {
			$totals['xAds'] = _setPriorityFromWeights($aRows['xAds']);
			}
			if (isset($aRows['ads']) && is_array($aRows['ads'])) {
			if (isset($aRows['lAds']) && is_array($aRows['lAds']) && count($aRows['lAds']) > 0) {
			$totals['ads'] = _getTotalPrioritiesByCP($aRows['ads'], true);
			} else {
			$totals['ads'] = _getTotalPrioritiesByCP($aRows['ads'], false);
			}
			}
			if (is_array($aRows['eAds'])) {
			$totals['eAds'] = _getTotalPrioritiesByCP($aRows['eAds']);
			}
			if (isset($aRows['lAds']) && is_array($aRows['lAds'])) {
			$totals['lAds'] = _setPriorityFromWeights($aRows['lAds']);
			}
			$aRows['priority'] = $totals;
			return $aRows;
}

function OA_Dal_Delivery_getAd($ad_id)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$ad_id = (int)$ad_id;
			$query = "
				SELECT
				d.bannerid AS ad_id,
				d.campaignid AS placement_id,
				d.status AS status,
				d.description AS name,
				d.storagetype AS type,
				d.contenttype AS contenttype,
				d.pluginversion AS pluginversion,
				d.filename AS filename,
				d.imageurl AS imageurl,
				d.htmltemplate AS htmltemplate,
				d.htmlcache AS htmlcache,
				d.width AS width,
				d.height AS height,
				d.weight AS weight,
				d.seq AS seq,
				d.target AS target,
				d.url AS url,
				d.alt AS alt,
				d.statustext AS statustext,
				d.bannertext AS bannertext,
				d.adserver AS adserver,
				d.block AS block_ad,
				d.capping AS cap_ad,
				d.session_capping AS session_cap_ad,
				d.compiledlimitation AS compiledlimitation,
				d.acl_plugins AS acl_plugins,
				d.prepend AS prepend,
				d.append AS append,
				d.bannertype AS bannertype,
				d.alt_filename AS alt_filename,
				d.alt_imageurl AS alt_imageurl,
				d.alt_contenttype AS alt_contenttype,
				d.parameters AS parameters,
				d.transparent AS transparent,
				d.ext_bannertype AS ext_bannertype,
				c.campaignid AS campaign_id,
				c.block AS block_campaign,
				c.capping AS cap_campaign,
				c.session_capping AS session_cap_campaign,
				c.show_capped_no_cookie AS show_capped_no_cookie,
				m.clientid AS client_id,
				c.clickwindow AS clickwindow,
				c.viewwindow AS viewwindow,
				m.advertiser_limitation AS advertiser_limitation,
				m.agencyid AS agency_id
			    FROM
				".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['banners'])." AS d,
				".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns'])." AS c,
				".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['clients'])." AS m
			    WHERE
				d.bannerid={$ad_id}
				AND
				d.campaignid = c.campaignid
				AND
				m.clientid = c.clientid
			    ";
			$rAd = OA_Dal_Delivery_query($query);
			if (!is_resource($rAd)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			} else {
			return (OA_Dal_Delivery_fetchAssoc($rAd));
			}
}
function OA_Dal_Delivery_getChannelLimitations($channelid)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$channelid = (int)$channelid;
			$rLimitation = OA_Dal_Delivery_query("
			    SELECT
				    acl_plugins,compiledlimitation
			    FROM
				    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['channel'])."
			    WHERE
				    channelid={$channelid}");
			if (!is_resource($rLimitation)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			}
			$limitations = OA_Dal_Delivery_fetchAssoc($rLimitation);
			return $limitations;
}
function OA_Dal_Delivery_getCreative($filename)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$rCreative = OA_Dal_Delivery_query("
				SELECT
				    contents,
				    t_stamp
				FROM
				    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['images'])."
				WHERE
				    filename = '".OX_escapeString($filename)."'
			    ");
			if (!is_resource($rCreative)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			} else {
			$aResult = OA_Dal_Delivery_fetchAssoc($rCreative);
			$aResult['t_stamp'] = strtotime($aResult['t_stamp'] . ' GMT');
			return ($aResult);
			}
}
function OA_Dal_Delivery_getTracker($trackerid)
{

			$conf = $GLOBALS['_MAX']['CONF'];
			$trackerid = (int)$trackerid;
			$rTracker = OA_Dal_Delivery_query("
				SELECT
				    t.clientid AS advertiser_id,
				    t.trackerid AS tracker_id,
				    t.trackername AS name,
				    t.variablemethod AS variablemethod,
				    t.description AS description,
				    t.viewwindow AS viewwindow,
				    t.clickwindow AS clickwindow,
				    t.blockwindow AS blockwindow,
				    t.appendcode AS appendcode
				FROM
				    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['trackers'])." AS t
				WHERE
				    t.trackerid={$trackerid}
			    ");
			if (!is_resource($rTracker)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			} else {
			return (OA_Dal_Delivery_fetchAssoc($rTracker));
			}
}
function OA_Dal_Delivery_getTrackerLinkedCreatives($trackerid = null)////29
{
			$aConf = $GLOBALS['_MAX']['CONF'];
			$trackerid = (int)$trackerid;
			echo $rCreatives = OA_Dal_Delivery_query("
				SELECT
				    b.bannerid AS ad_id,
				    b.campaignid AS placement_id,
				    c.viewwindow AS view_window,
				    c.clickwindow AS click_window,
				    ct.status AS status,
				    t.type AS tracker_type
				FROM
				    {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,
				    {$aConf['table']['prefix']}{$aConf['table']['campaigns_trackers']} AS ct,
				    {$aConf['table']['prefix']}{$aConf['table']['campaigns']} AS c,
				    {$aConf['table']['prefix']}{$aConf['table']['trackers']} AS t
				WHERE
				  ct.trackerid=t.trackerid
				  AND c.campaignid=b.campaignid
				  AND b.campaignid = ct.campaignid
				  " . ((!empty($trackerid)) ? ' AND t.trackerid='.$trackerid : '') . "
			    ");

exit;

			if (!is_resource($rCreatives)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			} else {
			$output = array();
			while ($aRow = OA_Dal_Delivery_fetchAssoc($rCreatives)) {
			$output[$aRow['ad_id']] = $aRow;
			}
			return $output;
			}
}
function OA_Dal_Delivery_getTrackerVariables($trackerid)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$trackerid = (int)$trackerid;
			$rVariables = OA_Dal_Delivery_query("
				SELECT
				    v.variableid AS variable_id,
				    v.trackerid AS tracker_id,
				    v.name AS name,
				    v.datatype AS type,
				    purpose AS purpose,
						reject_if_empty AS reject_if_empty,
						is_unique AS is_unique,
						unique_window AS unique_window,
				    v.variablecode AS variablecode
				FROM
				    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['variables'])." AS v
				WHERE
				    v.trackerid={$trackerid}
			    ");
			if (!is_resource($rVariables)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			} else {
			$output = array();
			while ($aRow = OA_Dal_Delivery_fetchAssoc($rVariables)) {
			$output[$aRow['variable_id']] = $aRow;
			}
			return $output;
			}
}
function OA_Dal_Delivery_getMaintenanceInfo()
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$result = OA_Dal_Delivery_query("
				SELECT
				    value AS maintenance_timestamp
				FROM
				    ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['application_variable'])."
				WHERE name = 'maintenance_timestamp'
			    ");
			if (!is_resource($result)) {
			return (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) ? OA_DELIVERY_CACHE_FUNCTION_ERROR : null;
			} else {
			$result = OA_Dal_Delivery_fetchAssoc($result);
			return $result['maintenance_timestamp'];
			}
}
function OA_Dal_Delivery_buildQuery($part, $lastpart, $precondition)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$aColumns = array(
			'd.bannerid AS ad_id',
			'd.campaignid AS placement_id',
			'd.status AS status',
			'd.description AS name',
			'd.storagetype AS type',
			'd.contenttype AS contenttype',
			'd.pluginversion AS pluginversion',
			'd.filename AS filename',
			'd.imageurl AS imageurl',
			'd.htmltemplate AS htmltemplate',
			'd.htmlcache AS htmlcache',
			'd.width AS width',
			'd.height AS height',
			'd.weight AS weight',
			'd.seq AS seq',
			'd.target AS target',
			'd.url AS url',
			'd.alt AS alt',
			'd.statustext AS statustext',
			'd.bannertext AS bannertext',
			'd.adserver AS adserver',
			'd.block AS block_ad',
			'd.capping AS cap_ad',
			'd.session_capping AS session_cap_ad',
			'd.compiledlimitation AS compiledlimitation',
			'd.acl_plugins AS acl_plugins',
			'd.prepend AS prepend',
			'd.append AS append',
			'd.bannertype AS bannertype',
			'd.alt_filename AS alt_filename',
			'd.alt_imageurl AS alt_imageurl',
			'd.alt_contenttype AS alt_contenttype',
			'd.parameters AS parameters',
			'd.transparent AS transparent',
			'd.ext_bannertype AS ext_bannertype',
			'az.priority AS priority',
			'az.priority_factor AS priority_factor',
			'az.to_be_delivered AS to_be_delivered',
			'm.campaignid AS campaign_id',
			'm.priority AS campaign_priority',
			'm.weight AS campaign_weight',
			'm.companion AS campaign_companion',
			'm.block AS block_campaign',
			'm.capping AS cap_campaign',
			'm.session_capping AS session_cap_campaign',
			'm.show_capped_no_cookie AS show_capped_no_cookie',
			'm.clickwindow AS clickwindow',
			'm.viewwindow AS viewwindow',
			'cl.clientid AS client_id',
			'm.expire_time AS expire_time',
			'm.revenue_type AS revenue_type',
			'm.ecpm_enabled AS ecpm_enabled',
			'm.ecpm AS ecpm',
			'cl.advertiser_limitation AS advertiser_limitation',
			'a.account_id AS account_id',
			'a.agencyid AS agency_id'
			);
			$aTables = array(
			"".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['banners'])." AS d",
			"JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns'])." AS m ON (d.campaignid = m.campaignid) ",
			"JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['clients'])." AS cl ON (m.clientid = cl.clientid) ",
			"JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['ad_zone_assoc'])." AS az ON (d.bannerid = az.ad_id)"
			);
			$select = "
			      az.zone_id = 0
			      AND m.status <= 0
			      AND d.status <= 0";
			if ($precondition != '')
			$select .= " $precondition ";
			if ($part != '')
			{
			$conditions = '';
			$onlykeywords = true;
			$part_array = explode(',', $part);
			for ($k=0; $k < count($part_array); $k++)
			{
			if (substr($part_array[$k], 0, 1) == '+' || substr($part_array[$k], 0, 1) == '_')
			{
			$operator = 'AND';
			$part_array[$k] = substr($part_array[$k], 1);
			}
			elseif (substr($part_array[$k], 0, 1) == '-')
			{
			$operator = 'NOT';
			$part_array[$k] = substr($part_array[$k], 1);
			}
			else
			$operator = 'OR';
			if($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if(preg_match('#^(?:size:)?([0-9]+x[0-9]+)$#', $part_array[$k], $m))
			{
			list($width, $height) = explode('x', $m[1]);
			if ($operator == 'OR')
			$conditions .= "OR (d.width = $width AND d.height = $height) ";
			elseif ($operator == 'AND')
			$conditions .= "AND (d.width = $width AND d.height = $height) ";
			else
			$conditions .= "AND (d.width != $width OR d.height != $height) ";
			$onlykeywords = false;
			}
			elseif (substr($part_array[$k],0,6) == 'width:')
			{
			$part_array[$k] = substr($part_array[$k], 6);
			if ($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if (is_int(strpos($part_array[$k], '-')))
			{
			list($min, $max) = explode('-', $part_array[$k]);
			if ($min == '')
			$min = 1;
			if ($max == '')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.width >= '".trim($min)."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.width >= '".trim($min)."' ";
			else
			$conditions .= "AND d.width < '".trim($min)."' ";
			}
			if ($max != '')
			{
			if ($operator == 'OR')
			$conditions .= "OR (d.width >= '".trim($min)."' AND d.width <= '".trim($max)."') ";
			elseif ($operator == 'AND')
			$conditions .= "AND (d.width >= '".trim($min)."' AND d.width <= '".trim($max)."') ";
			else
			$conditions .= "AND (d.width < '".trim($min)."' OR d.width > '".trim($max)."') ";
			}
			}
			else
			{
			if ($operator == 'OR')
			$conditions .= "OR d.width = '".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.width = '".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.width != '".trim($part_array[$k])."' ";
			}
			}
			$onlykeywords = false;
			}
			elseif (substr($part_array[$k],0,7) == 'height:')
			{
			$part_array[$k] = substr($part_array[$k], 7);
			if ($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if (is_int(strpos($part_array[$k], '-')))
			{
			list($min, $max) = explode('-', $part_array[$k]);
			if ($min == '')
			$min = 1;
			if ($max == '')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.height >= '".trim($min)."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.height >= '".trim($min)."' ";
			else
			$conditions .= "AND d.height < '".trim($min)."' ";
			}
			if ($max != '')
			{
			if ($operator == 'OR')
			$conditions .= "OR (d.height >= '".trim($min)."' AND d.height <= '".trim($max)."') ";
			elseif ($operator == 'AND')
			$conditions .= "AND (d.height >= '".trim($min)."' AND d.height <= '".trim($max)."') ";
			else
			$conditions .= "AND (d.height < '".trim($min)."' OR d.height > '".trim($max)."') ";
			}
			}
			else
			{
			if ($operator == 'OR')
			$conditions .= "OR d.height = '".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.height = '".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.height != '".trim($part_array[$k])."' ";
			}
			}
			$onlykeywords = false;
			}
			elseif (preg_match('#^(?:(?:bannerid|adid|ad_id):)?([0-9]+)$#', $part_array[$k], $m))
			{
			$part_array[$k] = $m[1];
			if ($part_array[$k])
			{
			if ($operator == 'OR')
			$conditions .= "OR d.bannerid='".$part_array[$k]."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.bannerid='".$part_array[$k]."' ";
			else
			$conditions .= "AND d.bannerid!='".$part_array[$k]."' ";
			}
			$onlykeywords = false;
			}
			elseif (preg_match('#^(?:(?:clientid|campaignid|placementid|placement_id):)?([0-9]+)$#', $part_array[$k], $m))
			{
			$part_array[$k] = $m[1];
			if ($part_array[$k])
			{
			if ($operator == 'OR')
			$conditions .= "OR d.campaignid='".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.campaignid='".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.campaignid!='".trim($part_array[$k])."' ";
			}
			$onlykeywords = false;
			}
			elseif (substr($part_array[$k], 0, 7) == 'format:')
			{
			$part_array[$k] = substr($part_array[$k], 7);
			if($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.contenttype='".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.contenttype='".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.contenttype!='".trim($part_array[$k])."' ";
			}
			$onlykeywords = false;
			}
			elseif($part_array[$k] == 'html')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.storagetype='html' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.storagetype='html' ";
			else
			$conditions .= "AND d.storagetype!='html' ";
			$onlykeywords = false;
			}
			elseif($part_array[$k] == 'textad')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.storagetype='txt' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.storagetype='txt' ";
			else
			$conditions .= "AND d.storagetype!='txt' ";
			$onlykeywords = false;
			}
			else
			{
			$conditions .= OA_Dal_Delivery_getKeywordCondition($operator, $part_array[$k]);
			}
			}
			}
			$conditions = strstr($conditions, ' ');
			if ($lastpart == true && $onlykeywords == true)
			$conditions .= OA_Dal_Delivery_getKeywordCondition('OR', 'global');
			if ($conditions != '') $select .= ' AND ('.$conditions.') ';
			}
			$columns = implode(",\n    ", $aColumns);
			$tables = implode("\n    ", $aTables);
			$leftJoin = "
				    LEFT JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['clients'])." AS c ON (c.clientid = m.clientid)
				    LEFT JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['agency'])." AS a ON (a.agencyid = c.agencyid)
			    ";
			$query = "SELECT\n    " . $columns . "\nFROM\n    " . $tables . $leftJoin . "\nWHERE " . $select;
			return $query;
}
function OA_Dal_Delivery_buildAdInfoQuery($part, $lastpart, $precondition)
{

			$conf = $GLOBALS['_MAX']['CONF'];
			$aColumns = array(
			'd.bannerid AS ad_id',
			'd.campaignid AS placement_id',
			'd.status AS status',
			'd.storagetype AS type',
			'd.contenttype AS contenttype',
			'd.weight AS weight',
			'd.width AS width',
			'd.ext_bannertype AS ext_bannertype',
			'd.height AS height',
			'd.adserver AS adserver',
			'd.block AS block_ad',
			'd.capping AS cap_ad',
			'd.session_capping AS session_cap_ad',
			'd.compiledlimitation AS compiledlimitation',
			'd.acl_plugins AS acl_plugins',
			'd.alt_filename AS alt_filename',
			'az.priority AS priority',
			'az.priority_factor AS priority_factor',
			'az.to_be_delivered AS to_be_delivered',
			'm.campaignid AS campaign_id',
			'm.priority AS campaign_priority',
			'm.weight AS campaign_weight',
			'm.companion AS campaign_companion',
			'm.block AS block_campaign',
			'm.capping AS cap_campaign',
			'm.session_capping AS session_cap_campaign',
			'm.show_capped_no_cookie AS show_capped_no_cookie',
			'cl.clientid AS client_id',
			'm.expire_time AS expire_time',
			'm.revenue_type AS revenue_type',
			'm.ecpm_enabled AS ecpm_enabled',
			'm.ecpm AS ecpm',
			'ct.status AS tracker_status',
			OX_Dal_Delivery_regex("d.htmlcache", "src\\s?=\\s?[\\'\"]http:")." AS html_ssl_unsafe",
			OX_Dal_Delivery_regex("d.imageurl", "^http:")." AS url_ssl_unsafe",
			);
			$aTables = array(
			"".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['banners'])." AS d",
			"JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['ad_zone_assoc'])." AS az ON (d.bannerid = az.ad_id)",
			"JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns'])." AS m ON (m.campaignid = d.campaignid) ",
			);
			$select = "
			      az.zone_id = 0
			      AND m.status <= 0
			      AND d.status <= 0";
			if ($precondition != '')
			$select .= " $precondition ";
			if ($part != '')
			{
			$conditions = '';
			$onlykeywords = true;
			$part_array = explode(',', $part);
			for ($k=0; $k < count($part_array); $k++)
			{
			if (substr($part_array[$k], 0, 1) == '+' || substr($part_array[$k], 0, 1) == '_')
			{
			$operator = 'AND';
			$part_array[$k] = substr($part_array[$k], 1);
			}
			elseif (substr($part_array[$k], 0, 1) == '-')
			{
			$operator = 'NOT';
			$part_array[$k] = substr($part_array[$k], 1);
			}
			else
			$operator = 'OR';
			if($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if(preg_match('#^(?:size:)?([0-9]+x[0-9]+)$#', $part_array[$k], $m))
			{
			list($width, $height) = explode('x', $m[1]);
			if ($operator == 'OR')
			$conditions .= "OR (d.width = $width AND d.height = $height) ";
			elseif ($operator == 'AND')
			$conditions .= "AND (d.width = $width AND d.height = $height) ";
			else
			$conditions .= "AND (d.width != $width OR d.height != $height) ";
			$onlykeywords = false;
			}
			elseif (substr($part_array[$k],0,6) == 'width:')
			{
			$part_array[$k] = substr($part_array[$k], 6);
			if ($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if (is_int(strpos($part_array[$k], '-')))
			{
			list($min, $max) = explode('-', $part_array[$k]);
			if ($min == '')
			$min = 1;
			if ($max == '')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.width >= '".trim($min)."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.width >= '".trim($min)."' ";
			else
			$conditions .= "AND d.width < '".trim($min)."' ";
			}
			if ($max != '')
			{
			if ($operator == 'OR')
			$conditions .= "OR (d.width >= '".trim($min)."' AND d.width <= '".trim($max)."') ";
			elseif ($operator == 'AND')
			$conditions .= "AND (d.width >= '".trim($min)."' AND d.width <= '".trim($max)."') ";
			else
			$conditions .= "AND (d.width < '".trim($min)."' OR d.width > '".trim($max)."') ";
			}
			}
			else
			{
			if ($operator == 'OR')
			$conditions .= "OR d.width = '".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.width = '".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.width != '".trim($part_array[$k])."' ";
			}
			}
			$onlykeywords = false;
			}
			elseif (substr($part_array[$k],0,7) == 'height:')
			{
			$part_array[$k] = substr($part_array[$k], 7);
			if ($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if (is_int(strpos($part_array[$k], '-')))
			{
			list($min, $max) = explode('-', $part_array[$k]);
			if ($min == '')
			$min = 1;
			if ($max == '')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.height >= '".trim($min)."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.height >= '".trim($min)."' ";
			else
			$conditions .= "AND d.height < '".trim($min)."' ";
			}
			if ($max != '')
			{
			if ($operator == 'OR')
			$conditions .= "OR (d.height >= '".trim($min)."' AND d.height <= '".trim($max)."') ";
			elseif ($operator == 'AND')
			$conditions .= "AND (d.height >= '".trim($min)."' AND d.height <= '".trim($max)."') ";
			else
			$conditions .= "AND (d.height < '".trim($min)."' OR d.height > '".trim($max)."') ";
			}
			}
			else
			{
			if ($operator == 'OR')
			$conditions .= "OR d.height = '".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.height = '".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.height != '".trim($part_array[$k])."' ";
			}
			}
			$onlykeywords = false;
			}
			elseif (preg_match('#^(?:(?:bannerid|adid|ad_id):)?([0-9]+)$#', $part_array[$k], $m))
			{
			$part_array[$k] = $m[1];
			if ($part_array[$k])
			{
			if ($operator == 'OR')
			$conditions .= "OR d.bannerid='".$part_array[$k]."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.bannerid='".$part_array[$k]."' ";
			else
			$conditions .= "AND d.bannerid!='".$part_array[$k]."' ";
			}
			$onlykeywords = false;
			}
			elseif (preg_match('#^(?:(?:clientid|campaignid|placementid|placement_id):)?([0-9]+)$#', $part_array[$k], $m))
			{
			$part_array[$k] = $m[1];
			if ($part_array[$k])
			{
			if ($operator == 'OR')
			$conditions .= "OR d.campaignid='".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.campaignid='".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.campaignid!='".trim($part_array[$k])."' ";
			}
			$onlykeywords = false;
			}
			elseif (substr($part_array[$k], 0, 7) == 'format:')
			{
			$part_array[$k] = substr($part_array[$k], 7);
			if($part_array[$k] != '' && $part_array[$k] != ' ')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.contenttype='".trim($part_array[$k])."' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.contenttype='".trim($part_array[$k])."' ";
			else
			$conditions .= "AND d.contenttype!='".trim($part_array[$k])."' ";
			}
			$onlykeywords = false;
			}
			elseif($part_array[$k] == 'html')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.storagetype='html' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.storagetype='html' ";
			else
			$conditions .= "AND d.storagetype!='html' ";
			$onlykeywords = false;
			}
			elseif($part_array[$k] == 'textad')
			{
			if ($operator == 'OR')
			$conditions .= "OR d.storagetype='txt' ";
			elseif ($operator == 'AND')
			$conditions .= "AND d.storagetype='txt' ";
			else
			$conditions .= "AND d.storagetype!='txt' ";
			$onlykeywords = false;
			}
			else
			{
			$conditions .= OA_Dal_Delivery_getKeywordCondition($operator, $part_array[$k]);
			}
			}
			}
			$conditions = strstr($conditions, ' ');
			if ($lastpart == true && $onlykeywords == true)
			$conditions .= OA_Dal_Delivery_getKeywordCondition('OR', 'global');
			if ($conditions != '') $select .= ' AND ('.$conditions.') ';
			}
			$columns = implode(",\n    ", $aColumns);
			$tables = implode("\n    ", $aTables);
			$leftJoin = "
				    LEFT JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['campaigns_trackers'])." AS ct ON (ct.campaignid = m.campaignid)
				    LEFT JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['clients'])." AS cl ON (cl.clientid = m.clientid)
				    LEFT JOIN ".OX_escapeIdentifier($conf['table']['prefix'].$conf['table']['agency'])." AS a ON (a.agencyid = cl.agencyid)
			    ";
			$query = "SELECT\n    " . $columns . "\nFROM\n    " . $tables . $leftJoin . "\nWHERE " . $select;
			return $query;
}
function _setPriorityFromWeights(&$aAds)
{
			if (!count($aAds)) {
			return 0;
			}
			$aCampaignWeights = array();
			$aCampaignAdWeight = array();
			foreach ($aAds as $v) {
			if (!isset($aCampaignWeights[$v['placement_id']])) {
			$aCampaignWeights[$v['placement_id']] = $v['campaign_weight'];
			$aCampaignAdWeight[$v['placement_id']] = 0;
			}
			$aCampaignAdWeight[$v['placement_id']] += $v['weight'];
			}
			foreach ($aCampaignWeights as $k => $v) {
			if ($aCampaignAdWeight[$k]) {
			$aCampaignWeights[$k] /= $aCampaignAdWeight[$k];
			}
			}
			$totalPri = 0;
			foreach ($aAds as $k => $v) {
			$aAds[$k]['priority'] = $aCampaignWeights[$v['placement_id']] * $v['weight'];
			$totalPri += $aAds[$k]['priority'];
			}
			if ($totalPri) {
			foreach ($aAds as $k => $v) {
			$aAds[$k]['priority'] /= $totalPri;
			}
			return 1;
			}
			return 0;
}
function _getTotalPrioritiesByCP($aAdsByCP, $includeBlank = true)
{
			$totals = array();
			$total_priority_cp = array();
			$blank_priority = 1;
			foreach ($aAdsByCP as $campaign_priority => $aAds) {
			$total_priority_cp[$campaign_priority] = 0;
			foreach ($aAds as $key => $aAd) {
			$blank_priority -= (double)$aAd['priority'];
			if ($aAd['to_be_delivered']) {
			$priority = $aAd['priority'] * $aAd['priority_factor'];
			} else {
			$priority = 0.00001;
			}
			$total_priority_cp[$campaign_priority] += $priority;
			}
			}
			$total_priority = 0;
			if ($includeBlank) {
			$total_priority = $blank_priority <= 1e-15 ? 0 : $blank_priority;
			}
			ksort($total_priority_cp);
			foreach($total_priority_cp as $campaign_priority => $priority) {
			$total_priority += $priority;
			if ($total_priority) {
			$totals[$campaign_priority] = $priority / $total_priority;
			} else {
			$totals[$campaign_priority] = 0;
			}
			}
			return $totals;
}

function MAX_Dal_Delivery_Include()///21
{
			static $included;
			if (isset($included)) {
			return;
			}
			$included = true;
			$conf = $GLOBALS['_MAX']['CONF'];
			if (isset($conf['origin']['type']) && is_readable(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php')) {
			require(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php');
			} else {
			require(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['database']['type']) . '.php');
			}
}


function MAX_trackerbuildJSVariablesScript($trackerid, $conversionInfo, $trackerJsCode = null)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$buffer = '';
			$url = MAX_commonGetDeliveryUrl($conf['file']['conversionvars']);
			$tracker = MAX_cacheGetTracker($trackerid);
			$variables = MAX_cacheGetTrackerVariables($trackerid);
			$variableQuerystring = '';
			if (empty($trackerJsCode)) {
			$trackerJsCode = md5(uniqid('', true));
			} else {
			$tracker['variablemethod'] = 'default';
			}
			if (!empty($variables)) {
			if ($tracker['variablemethod'] == 'dom') {
			$buffer .= "
			    function MAX_extractTextDom(o)
			    {
				var txt = '';

				if (o.nodeType == 3) {
				    txt = o.data;
				} else {
				    for (var i = 0; i < o.childNodes.length; i++) {
					txt += MAX_extractTextDom(o.childNodes[i]);
				    }
				}

				return txt;
			    }

			    function MAX_TrackVarDom(id, v)
			    {
				if (max_trv[id][v]) { return; }
				var o = document.getElementById(v);
				if (o) {
				    max_trv[id][v] = escape(o.tagName == 'INPUT' ? o.value : MAX_extractTextDom(o));
				}
			    }";
			$funcName = 'MAX_TrackVarDom';
			} elseif ($tracker['variablemethod'] == 'default') {
			$buffer .= "
			    function MAX_TrackVarDefault(id, v)
			    {
				if (max_trv[id][v]) { return; }
				if (typeof(window[v]) == undefined) { return; }
				max_trv[id][v] = window[v];
			    }";
			$funcName = 'MAX_TrackVarDefault';
			} else {
			$buffer .= "
			    function MAX_TrackVarJs(id, v, c)
			    {
				if (max_trv[id][v]) { return; }
				if (typeof(window[v]) == undefined) { return; }
				if (typeof(c) != 'undefined') {
				    eval(c);
				}
				max_trv[id][v] = window[v];
			    }";
			$funcName = 'MAX_TrackVarJs';
			}
			$buffer .= "
			    if (!max_trv) { var max_trv = new Array(); }
			    if (!max_trv['{$trackerJsCode}']) { max_trv['{$trackerJsCode}'] = new Array(); }";
			foreach($variables as $key => $variable) {
			$variableQuerystring .= "&{$variable['name']}=\"+max_trv['{$trackerJsCode}']['{$variable['name']}']+\"";
			if ($tracker['variablemethod'] == 'custom') {
			$buffer .= "
			    {$funcName}('{$trackerJsCode}', '{$variable['name']}', '".addcslashes($variable['variablecode'], "'")."');";
			} else {
			$buffer .= "
			    {$funcName}('{$trackerJsCode}', '{$variable['name']}');";
			}
			}
			if (!empty($variableQuerystring)) {
			$conversionInfoParams = array();
			foreach ($conversionInfo as $plugin => $pluginData) {
			if (is_array($pluginData)) {
			foreach ($pluginData as $key => $value) {
			$conversionInfoParams[] = $key . '=' . urlencode($value);
			}
			}
			}
			$conversionInfoParams = '&' . implode('&', $conversionInfoParams);
			$buffer .= "
			    document.write (\"<\" + \"script language='JavaScript' type='text/javascript' src='\");
			    document.write (\"$url?trackerid=$trackerid{$conversionInfoParams}{$variableQuerystring}'\");";
			$buffer .= "\n\tdocument.write (\"><\\/scr\"+\"ipt>\");";
			}
			}
			if(!empty($tracker['appendcode'])) {
			$tracker['appendcode'] = preg_replace('/("\?trackerid=\d+&amp;inherit)=1/', '$1='.$trackerJsCode, $tracker['appendcode']);
			$jscode = MAX_javascriptToHTML($tracker['appendcode'], "MAX_{$trackerid}_appendcode");
			$jscode = preg_replace("/\{m3_trackervariable:(.+?)\}/", "\"+max_trv['{$trackerJsCode}']['$1']+\"", $jscode);
			$buffer .= "\n".preg_replace('/^/m', "\t", $jscode)."\n";
			}
			if (empty($buffer)) {
			$buffer = "document.write(\"\");";
			}
			return $buffer;
}
//////DAC015
function GetTrackerLinkedCreatives($trackerid)
{
$aConf = $GLOBALS['_MAX']['CONF'];

$transaction_id=$_REQUEST['transaction_id'];

$query_tranaction="select * from djax_s2s_track  where transaction_id='$transaction_id'";

$res = OA_Dal_Delivery_query($query_tranaction);

$results =OA_Dal_Delivery_fetchAssoc($res);

			$rCreatives = OA_Dal_Delivery_query("
				SELECT
				    b.bannerid AS ad_id,
				    b.campaignid AS placement_id,
				    c.viewwindow AS view_window,
				    c.clickwindow AS click_window,
				    ct.status AS status,
				    t.type AS tracker_type
				FROM
				    {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,
				    {$aConf['table']['prefix']}{$aConf['table']['campaigns_trackers']} AS ct,
				    {$aConf['table']['prefix']}{$aConf['table']['campaigns']} AS c,
				    {$aConf['table']['prefix']}{$aConf['table']['trackers']} AS t
				WHERE
				  ct.trackerid=t.trackerid
				  AND c.campaignid=b.campaignid
				  AND b.campaignid = ct.campaignid
				  AND  b.bannerid='".$results['ad_id']."'
				  " . ((!empty($trackerid)) ? ' AND t.trackerid='.$trackerid : '') . "
			    ");
		
			$output = array();

			while ($aRow = OA_Dal_Delivery_fetchAssoc($rCreatives))
			{
			$output[$aRow['ad_id']] = $aRow;
			}
			return $output;
}
//////DAC015
function MAX_trackerCheckForValidAction($trackerid)//////22
{

			$aConf = $GLOBALS['_MAX']['CONF'];

			$aTrackerLinkedAds = GetTrackerLinkedCreatives($trackerid);

			if (empty($aTrackerLinkedAds))
			{
			return false;
			}

			$aPossibleActions = _getActionTypes();

			$now = MAX_commonGetTimeNow();

			$aConf = $GLOBALS['_MAX']['CONF'];

			$aMatchingActions = array();

			$transaction_id=$_REQUEST['transaction_id'];

			$query_tranaction="select * from djax_s2s_track where transaction_id='$transaction_id'";

			$res = OA_Dal_Delivery_query($query_tranaction);

			$results =OA_Dal_Delivery_fetchAssoc($res); 

			$zoneId=$results['zone_id'];
	
			$check_link="select * from rv_ad_zone_assoc where zone_id='".$results['zone_id']."' and ad_id='".$results['ad_id']."'";


			if(OA_Dal_Delivery_numRows(OA_Dal_Delivery_query($check_link)==0))
			{
				return false;
			}

			foreach ($aTrackerLinkedAds as $creativeId => $aLinkedInfo)
			{
				foreach ($aPossibleActions as $actionId => $action)
				{
					
						$lastAction = MAX_commonUnCompressInt($lastAction);

						$lastSeenSecondsAgo = $now - $lastAction;
					
						$aMatchingActions[$lastSeenSecondsAgo] = array(
						'action_type' => $actionId,
						'tracker_type' => $aLinkedInfo['tracker_type'],
						'status' => $aLinkedInfo['status'],
						'cid' => $creativeId,
						'zid' => $zoneId,
						'dt' => $now,
						'window' => $aLinkedInfo[$action . '_window'],
						'extra' => $extra,
						);
						
					
				}
			}

			if (empty($aMatchingActions))
			{
			return false;
			}

			ksort($aMatchingActions);


			return array_shift($aMatchingActions);
}
function _getActionTypes()////////23
{
			return array(0 => 'view', 1 => 'click');
}
function _getTrackerTypes()
{
			return array(1 => 'sale', 2 => 'lead', 3 => 'signup');
}

function MAX_Delivery_log_logAdRequest($adId, $zoneId, $aAd = array())
{
			if (empty($GLOBALS['_MAX']['CONF']['logging']['adRequests'])) { return true; }
			OX_Delivery_Common_hook('logRequest', array($adId, $zoneId, $aAd, _viewersHostOkayToLog($adId, $zoneId)));
}
function MAX_Delivery_log_logAdImpression($adId, $zoneId)
{
			if (empty($GLOBALS['_MAX']['CONF']['logging']['adImpressions'])) { return true; }
			OX_Delivery_Common_hook('logImpression', array($adId, $zoneId, _viewersHostOkayToLog($adId, $zoneId)));
}
function MAX_Delivery_log_logAdClick($adId, $zoneId)
{
			if (empty($GLOBALS['_MAX']['CONF']['logging']['adClicks'])) { return true; }
			OX_Delivery_Common_hook('logClick', array($adId, $zoneId, _viewersHostOkayToLog($adId, $zoneId)));
}
function MAX_Delivery_log_logConversion($trackerId, $aConversion)
{
			if (empty($GLOBALS['_MAX']['CONF']['logging']['trackerImpressions'])) { return true; }
			$aConf = $GLOBALS['_MAX']['CONF'];
			if (!empty($aConf['lb']['enabled'])) {
			$aConf['rawDatabase']['host'] = $_SERVER['SERVER_ADDR'];
			} else {
			$aConf['rawDatabase']['host'] = 'singleDB';
			}
			if (isset($aConf['rawDatabase']['serverRawIp'])) {
			$serverRawIp = $aConf['rawDatabase']['serverRawIp'];
			} else {
			$serverRawIp = $aConf['rawDatabase']['host'];
			}
			$aConversionInfo = OX_Delivery_Common_hook('logConversion', array($trackerId, $serverRawIp, $aConversion, _viewersHostOkayToLog(null, null, $trackerId)));
			if (is_array($aConversionInfo)) {
			return $aConversionInfo;
			}
			return false;
}
function MAX_Delivery_log_logVariableValues($aVariables, $trackerId, $serverConvId, $serverRawIp)
{
			$aConf = $GLOBALS['_MAX']['CONF'];
			foreach ($aVariables as $aVariable) {
			if (isset($_GET[$aVariable['name']])) {
			$value = $_GET[$aVariable['name']];
			if (!strlen($value) || $value == 'undefined') {
			unset($aVariables[$aVariable['variable_id']]);
			continue;
			}
			switch ($aVariable['type']) {
			case 'int':
			case 'numeric':
			$value = preg_replace('/[^0-9.]/', '', $value);
			$value = floatval($value);
			break;
			case 'date':
			if (!empty($value)) {
			$value = date('Y-m-d H:i:s', strtotime($value));
			} else {
			$value = '';
			}
			break;
			}
			} else {
			unset($aVariables[$aVariable['variable_id']]);
			continue;
			}
			$aVariables[$aVariable['variable_id']]['value'] = $value;
			}
			if (count($aVariables)) {
			OX_Delivery_Common_hook('logConversionVariable', array($aVariables, $trackerId, $serverConvId, $serverRawIp, _viewersHostOkayToLog(null, null, $trackerId)));
			}
}
function _viewersHostOkayToLog($adId=0, $zoneId=0, $trackerId=0)
{
			$aConf = $GLOBALS['_MAX']['CONF'];
			$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
			$okToLog = true;
			if (!empty($aConf['logging']['enforceUserAgents'])) {
			$aKnownBrowsers = explode('|', strtolower($aConf['logging']['enforceUserAgents']));
			$allowed = false;
			foreach ($aKnownBrowsers as $browser) {
			if (strpos($agent, $browser) !== false) {
			$allowed = true;
			break;
			}
			}
			OX_Delivery_logMessage('user-agent browser : '.$agent.' is '.($allowed ? '' : 'not ').'allowed', 7);
			if (!$allowed) {
			$GLOBALS['_MAX']['EVENT_FILTER_FLAGS'][] = 'enforceUserAgents';
			$okToLog = false;
			}
			}
			if (!empty($aConf['logging']['ignoreUserAgents'])) {
			$aKnownBots = explode('|', strtolower($aConf['logging']['ignoreUserAgents']));
			foreach ($aKnownBots as $bot) {
			if (strpos($agent, $bot) !== false) {
			OX_Delivery_logMessage('user-agent '.$agent.' is a known bot '.$bot, 7);
			$GLOBALS['_MAX']['EVENT_FILTER_FLAGS'][] = 'ignoreUserAgents';
			$okToLog = false;
			}
			}
			}
			if (!empty($aConf['logging']['ignoreHosts'])) {
			$hosts = str_replace(',', '|', $aConf['logging']['ignoreHosts']);
			$hosts = '#^('.$hosts.')$#i';
			$hosts = str_replace('.', '\.', $hosts);
			$hosts = str_replace('*', '[^.]+', $hosts);
			if (preg_match($hosts, $_SERVER['REMOTE_ADDR'])) {
			OX_Delivery_logMessage('viewer\'s ip is in the ignore list '.$_SERVER['REMOTE_ADDR'], 7);
			$GLOBALS['_MAX']['EVENT_FILTER_FLAGS'][] = 'ignoreHosts_ip';
			$okToLog = false;
			}
			if (preg_match($hosts, $_SERVER['REMOTE_HOST'])) {
			OX_Delivery_logMessage('viewer\'s host is in the ignore list '.$_SERVER['REMOTE_HOST'], 7);
			$GLOBALS['_MAX']['EVENT_FILTER_FLAGS'][] = 'ignoreHosts_host';
			$okToLog = false;
			}
			}
			if ($okToLog) OX_Delivery_logMessage('viewer\'s host is OK to log', 7);
			$result = OX_Delivery_Common_Hook('filterEvent', array($adId, $zoneId, $trackerId));
			if (!empty($result) && is_array($result)) {
			foreach ($result as $pci => $value) {
			if ($value == true) {
			$GLOBALS['_MAX']['EVENT_FILTER_FLAGS'][] = $pci;
			$okToLog = false;
			}
			}
			}
			return $okToLog;
}
function MAX_Delivery_log_getArrGetVariable($name)
{
			$varName = $GLOBALS['_MAX']['CONF']['var'][$name];
			return isset($_GET[$varName]) ? explode($GLOBALS['_MAX']['MAX_DELIVERY_MULTIPLE_DELIMITER'], $_GET[$varName]) : array();
}
function MAX_Delivery_log_ensureIntegerSet(&$aArray, $index)
{
			if (!is_array($aArray)) {
			$aArray = array();
			}
			if (empty($aArray[$index])) {
			$aArray[$index] = 0;
			} else {
			if (!is_integer($aArray[$index])) {
			$aArray[$index] = intval($aArray[$index]);
			}
			}
}
function MAX_Delivery_log_setAdLimitations($index, $aAds, $aCaps)
{
			_setLimitations('Ad', $index, $aAds, $aCaps);
}
function MAX_Delivery_log_setCampaignLimitations($index, $aCampaigns, $aCaps)
{
			_setLimitations('Campaign', $index, $aCampaigns, $aCaps);
}
function MAX_Delivery_log_setZoneLimitations($index, $aZones, $aCaps)
{
			_setLimitations('Zone', $index, $aZones, $aCaps);
}
function MAX_Delivery_log_setLastAction($index, $aAdIds, $aZoneIds, $aSetLastSeen, $action = 'view')
{

			$aConf = $GLOBALS['_MAX']['CONF'];
			if (!empty($aSetLastSeen[$index])) {
			$cookieData = MAX_commonCompressInt(MAX_commonGetTimeNow()) . "-" . $aZoneIds[$index];
			$conversionParams = OX_Delivery_Common_hook('addConversionParams', array(&$index, &$aAdIds, &$aZoneIds, &$aSetLastSeen, &$action, &$cookieData));
			if (!empty($conversionParams) && is_array($conversionParams)) {
			foreach ($conversionParams as $params) {
			if (!empty($params) && is_array($params)) {
			foreach ($params as $key => $value) {
			$cookieData .= " {$value}";
			}
			}
			}
			}
			MAX_cookieAdd("_{$aConf['var']['last' . ucfirst($action)]}[{$aAdIds[$index]}]", $cookieData, _getTimeThirtyDaysFromNow());
			}
}
function MAX_Delivery_log_setClickBlocked($index, $aAdIds)
{
			$aConf = $GLOBALS['_MAX']['CONF'];
			MAX_cookieAdd("_{$aConf['var']['blockLoggingClick']}[{$aAdIds[$index]}]", MAX_commonCompressInt(MAX_commonGetTimeNow()), _getTimeThirtyDaysFromNow());
}
function MAX_Delivery_log_isClickBlocked($adId, $aBlockLoggingClick)
{
			if (isset($GLOBALS['conf']['logging']['blockAdClicksWindow']) && $GLOBALS['conf']['logging']['blockAdClicksWindow'] != 0) {
			if (isset($aBlockLoggingClick[$adId])) {
			$endBlock = MAX_commonUnCompressInt($aBlockLoggingClick[$adId]) + $GLOBALS['conf']['logging']['blockAdClicksWindow'];
			if ($endBlock >= MAX_commonGetTimeNow()) {
			OX_Delivery_logMessage('adID '.$adId.' click is still blocked by block logging window ', 7);
			return true;
			}
			}
			}
			return false;
}
function _setLimitations($type, $index, $aItems, $aCaps)
{
			MAX_Delivery_log_ensureIntegerSet($aCaps['block'], $index);
			MAX_Delivery_log_ensureIntegerSet($aCaps['capping'], $index);
			MAX_Delivery_log_ensureIntegerSet($aCaps['session_capping'], $index);
			MAX_Delivery_cookie_setCapping(
			$type,
			$aItems[$index],
			$aCaps['block'][$index],
			$aCaps['capping'][$index],
			$aCaps['session_capping'][$index]
			);
}

function MAX_commonGetDeliveryUrl($file = null)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			if ($GLOBALS['_MAX']['SSL_REQUEST']) {
			$url = MAX_commonConstructSecureDeliveryUrl($file);
			} else {
			$url = MAX_commonConstructDeliveryUrl($file);
			}
			return $url;
}
function MAX_commonConstructDeliveryUrl($file)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			return 'http://' . $conf['webpath']['delivery'] . '/' . $file;
}
function MAX_commonConstructSecureDeliveryUrl($file)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			if ($conf['openads']['sslPort'] != 443) {
			$path = preg_replace('#/#', ':' . $conf['openads']['sslPort'] . '/', $conf['webpath']['deliverySSL'], 1);
			} else {
			$path = $conf['webpath']['deliverySSL'];
			}
			return 'https://' . $path . '/' . $file;
}
function MAX_commonConstructPartialDeliveryUrl($file, $ssl = false)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			if ($ssl) {
			return '//' . $conf['webpath']['deliverySSL'] . '/' . $file;
			} else {
			return '//' . $conf['webpath']['delivery'] . '/' . $file;
			}
}
function MAX_commonRemoveSpecialChars(&$var)
{
			static $magicQuotes;
			if (!isset($magicQuotes)) {
			$magicQuotes = get_magic_quotes_gpc();
			}
			if (isset($var)) {
			if (!is_array($var)) {
			if ($magicQuotes) {
			$var = stripslashes($var);
			}
			$var = strip_tags($var);
			$var = str_replace(array("\n", "\r"), array('', ''), $var);
			$var = trim($var);
			} else {
			array_walk($var, 'MAX_commonRemoveSpecialChars');
			}
			}
}
function MAX_commonConvertEncoding($content, $toEncoding, $fromEncoding = 'UTF-8', $aExtensions = null)
{
			if (($toEncoding == $fromEncoding) || empty($toEncoding)) {
						return $content;
			}
			if (!isset($aExtensions) || !is_array($aExtensions)) {
			$aExtensions = array('iconv', 'mbstring', 'xml');
			}
			if (is_array($content)) {
			foreach ($content as $key => $value) {
			$content[$key] = MAX_commonConvertEncoding($value, $toEncoding, $fromEncoding, $aExtensions);
			}
			return $content;
			} else {
			$toEncoding = strtoupper($toEncoding);
			$fromEncoding = strtoupper($fromEncoding);
			$aMap = array();
			$aMap['mbstring']['WINDOWS-1255'] = 'ISO-8859-8';  $aMap['xml']['ISO-8859-15'] = 'ISO-8859-1';   $converted = false;
			foreach ($aExtensions as $extension) {
			$mappedFromEncoding = isset($aMap[$extension][$fromEncoding]) ? $aMap[$extension][$fromEncoding] : $fromEncoding;
			$mappedToEncoding = isset($aMap[$extension][$toEncoding]) ? $aMap[$extension][$toEncoding] : $toEncoding;
			switch ($extension) {
			case 'iconv':
			if (function_exists('iconv')) {
			$converted = @iconv($mappedFromEncoding, $mappedToEncoding, $content);
			}
			break;
			case 'mbstring':
			if (function_exists('mb_convert_encoding')) {
			$converted = @mb_convert_encoding($content, $mappedToEncoding, $mappedFromEncoding);
			}
			break;
			case 'xml':
			if (function_exists('utf8_encode')) {
			if ($mappedToEncoding == 'UTF-8' && $mappedFromEncoding == 'ISO-8859-1') {
			$converted = utf8_encode($content);
			} elseif ($mappedToEncoding == 'ISO-8859-1' && $mappedFromEncoding == 'UTF-8') {
			$converted = utf8_decode($content);
			}
			}
			break;
			}
			}
			return $converted ? $converted : $content;
			}
}
function MAX_commonSendContentTypeHeader($type = 'text/html', $charset = null)
{
			$header = 'Content-type: ' . $type;
			if (!empty($charset) && preg_match('/^[a-zA-Z0-9_-]+$/D', $charset)) {
			$header .= '; charset=' . $charset;
			}
			MAX_header($header);
}
function MAX_commonSetNoCacheHeaders()///28
{
			MAX_header('Pragma: no-cache');
			MAX_header('Cache-Control: private, max-age=0, no-cache');
			MAX_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
}
function MAX_commonAddslashesRecursive($a)
{
			if (is_array($a)) {
			reset($a);
			while (list($k,$v) = each($a)) {
			$a[$k] = MAX_commonAddslashesRecursive($v);
			}
			reset ($a);
			return ($a);
			} else {
			return is_null($a) ? null : addslashes($a);
			}
}
function MAX_commonRegisterGlobalsArray($args = array())////25
{
			static $magic_quotes_gpc;
			if (!isset($magic_quotes_gpc)) {
			$magic_quotes_gpc = ini_get('magic_quotes_gpc');
			}
			$found = false;
			foreach($args as $key) {
			if (isset($_GET[$key])) {
			$value = $_GET[$key];
			$found = true;
			}
			if (isset($_POST[$key])) {
			$value = $_POST[$key];
			$found = true;
			}
			if ($found) {
			if (!$magic_quotes_gpc) {
			if (!is_array($value)) {
			$value = addslashes($value);
			} else {
			$value = MAX_commonAddslashesRecursive($value);
			}
			}
			$GLOBALS[$key] = $value;
			$found = false;
			}
			}
}
function MAX_commonDeriveSource($source)//////26
{
			return MAX_commonEncrypt(trim(urldecode($source)));
}
function MAX_commonEncrypt($string)/////27
{
			$convert = '';
			if (isset($string) && substr($string,1,4) != 'obfs' && $GLOBALS['_MAX']['CONF']['delivery']['obfuscate']) {
			$strLen = strlen($string);
			for ($i=0; $i < $strLen; $i++) {
			$dec = ord(substr($string,$i,1));
			if (strlen($dec) == 2) {
			$dec = 0 . $dec;
			}
			$dec = 324 - $dec;
			$convert .= $dec;
			}
			$convert = '{obfs:' . $convert . '}';
			return ($convert);
			} else {
			return $string;
			}
}
function MAX_commonDecrypt($string)
{
			$conf = $GLOBALS['_MAX']['CONF'];
			$convert = '';
			if (isset($string) && substr($string,1,4) == 'obfs' && $conf['delivery']['obfuscate']) {
			$strLen = strlen($string);
			for ($i=6; $i < $strLen-1; $i = $i+3) {
			$dec = substr($string,$i,3);
			$dec = 324 - $dec;
			$dec = chr($dec);
			$convert .= $dec;
			}
			return ($convert);
			} else {
			return($string);
			}
}
function MAX_commonInitVariables()///24
{
			MAX_commonRegisterGlobalsArray(array('context', 'source', 'target', 'withText', 'withtext', 'ct0', 'what', 'loc', 'referer', 'zoneid', 'campaignid', 'bannerid', 'clientid', 'charset'));
			global $context, $source, $target, $withText, $withtext, $ct0, $what, $loc, $referer, $zoneid, $campaignid, $bannerid, $clientid, $charset;
			if (isset($withText) && !isset($withtext)) $withtext = $withText;
			$withtext = (isset($withtext) && is_numeric($withtext) ? $withtext : 0 );
			$ct0 = (isset($ct0) ? $ct0 : '' );
			$context = (isset($context) ? $context : array() );
			$target = (isset($target) && (!empty($target)) && (!strpos($target , chr(32))) ? $target : '' );
			$charset = (isset($charset) && (!empty($charset)) && (!strpos($charset, chr(32))) ? $charset : 'UTF-8' );
			$bannerid = (isset($bannerid) && is_numeric($bannerid) ? $bannerid : '' );
			$campaignid = (isset($campaignid) && is_numeric($campaignid) ? $campaignid : '' );
			$clientid = (isset($clientid) && is_numeric($clientid) ? $clientid : '' );
			$zoneid = (isset($zoneid) && is_numeric($zoneid) ? $zoneid : '' );
			if (!isset($what))
			{
			if (!empty($bannerid)) {
			$what = 'bannerid:'.$bannerid;
			} elseif (!empty($campaignid)) {
			$what = 'campaignid:'.$campaignid;
			} elseif (!empty($zoneid)) {
			$what = 'zone:'.$zoneid;
			} else {
			$what = '';
			}
			}
			elseif (preg_match('/^([a-z]+):(\d+)$/', $what, $matches))
			{
			switch ($matches[1])
			{
			case 'zoneid':
			case 'zone':
			$zoneid = $matches[2];
			break;
			case 'bannerid':
			$bannerid = $matches[2];
			break;
			case 'campaignid':
			$campaignid = $matches[2];
			break;
			case 'clientid':
			$clientid = $matches[2];
			break;
			}
			}
			if (!isset($clientid)) $clientid = '';
			if (empty($campaignid)) $campaignid = $clientid;
			$source = MAX_commonDeriveSource($source);
			if (!empty($loc)) {
			$loc = stripslashes($loc);
			} elseif (!empty($_SERVER['HTTP_REFERER'])) {
			$loc = $_SERVER['HTTP_REFERER'];
			} else {
			$loc = '';
			}
			if (!empty($referer)) {
			$_SERVER['HTTP_REFERER'] = stripslashes($referer);
			} else {
			if (isset($_SERVER['HTTP_REFERER'])) unset($_SERVER['HTTP_REFERER']);
			}
			$GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'] = array(
			$GLOBALS['_MAX']['CONF']['var']['blockAd'],
			$GLOBALS['_MAX']['CONF']['var']['capAd'],
			$GLOBALS['_MAX']['CONF']['var']['sessionCapAd'],
			$GLOBALS['_MAX']['CONF']['var']['blockCampaign'],
			$GLOBALS['_MAX']['CONF']['var']['capCampaign'],
			$GLOBALS['_MAX']['CONF']['var']['sessionCapCampaign'],
			$GLOBALS['_MAX']['CONF']['var']['blockZone'],
			$GLOBALS['_MAX']['CONF']['var']['capZone'],
			$GLOBALS['_MAX']['CONF']['var']['sessionCapZone'],
			$GLOBALS['_MAX']['CONF']['var']['lastClick'],
			$GLOBALS['_MAX']['CONF']['var']['lastView'],
			$GLOBALS['_MAX']['CONF']['var']['blockLoggingClick'],
			);
			if (strtolower($charset) == 'unicode') { $charset = 'utf-8'; }
}
function MAX_commonDisplay1x1()//30
{
			MAX_header('Content-Type: image/gif');
			MAX_header('Content-Length: 43');
			echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
}
function MAX_commonGetTimeNow()//28
{
			if (!isset($GLOBALS['_MAX']['NOW'])) {
			$GLOBALS['_MAX']['NOW'] = time();
			}
			return $GLOBALS['_MAX']['NOW'];
}
function MAX_getRandomNumber($length = 10)
{
			return substr(md5(uniqid(time(), true)), 0, $length);
}
function MAX_header($value)///35
{
			 header($value);
}
function MAX_redirect($url)
{
			if (!preg_match('/^(?:javascript|data):/i', $url)) {
			header('Location: '.$url);
			MAX_sendStatusCode(302);
			}
}
function MAX_sendStatusCode($iStatusCode)
{
			$aConf = $GLOBALS['_MAX']['CONF'];
			$arr = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '[Unused]',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
			);
			if (isset($arr[$iStatusCode])) {
			$text = $iStatusCode . ' ' . $arr[$iStatusCode];
			if (!empty($aConf['delivery']['cgiForceStatusHeader']) && strpos(php_sapi_name(), 'cgi') !== 0) {
			MAX_header('Status: ' . $text);
			} else {
			MAX_header($_SERVER["SERVER_PROTOCOL"] .' ' . $text);
			}
			}
}
function MAX_commonPackContext($context = array())
{
			$include = array();
			$exclude = array();
			foreach ($context as $idx => $value) {
			reset($value);
			list($key, $value) = each($value);
			list($item,$id) = explode(':', $value);
			switch ($item) {
			case 'campaignid': $value = 'c:' . $id; break;
			case 'clientid': $value = 'a:' . $id; break;
			case 'bannerid': $value = 'b:' . $id; break;
			case 'companionid': $value = 'p:' . $id; break;
			}
			switch ($key) {
			case '!=': $exclude[$value] = true; break;
			case '==': $include[$value] = true; break;
			}
			}
			$exclude = array_keys($exclude);
			$include = array_keys($include);
			return base64_encode(implode('#', $exclude) . '|' . implode('#', $include));
}
function MAX_commonUnpackContext($context = '')
{
			list($exclude,$include) = explode('|', base64_decode($context));
			return array_merge(_convertContextArray('!=', explode('#', $exclude)), _convertContextArray('==', explode('#', $include)));
}
function MAX_commonCompressInt($int)
{
			return base_convert($int, 10, 36);
}
function MAX_commonUnCompressInt($string)
{
			return base_convert($string, 36, 10);
}
function _convertContextArray($key, $array)
{
			$unpacked = array();
			foreach ($array as $value) {
			if (empty($value)) { continue; }
			list($item, $id) = explode(':', $value);
			switch ($item) {
			case 'c': $unpacked[] = array($key => 'campaignid:' . $id); break;
			case 'a': $unpacked[] = array($key => 'clientid:' . $id); break;
			case 'b': $unpacked[] = array($key => 'bannerid:' . $id); break;
			case 'p': $unpacked[] = array($key => 'companionid:'.$id); break;
			}
			}
			return $unpacked;
}
function OX_Delivery_Common_hook($hookName, $aParams = array(), $functionName = '')////32
{
			$return = null;
			if (!empty($functionName)) {
			$aParts = explode(':', $functionName);
			if (count($aParts) === 3) {
			$functionName = OX_Delivery_Common_getFunctionFromComponentIdentifier($functionName, $hookName);
			}
			if (function_exists($functionName)) {
			$return = call_user_func_array($functionName, $aParams);
			}
			} else {
			if (!empty($GLOBALS['_MAX']['CONF']['deliveryHooks'][$hookName])) {
			$return = array();
			$hooks = explode('|', $GLOBALS['_MAX']['CONF']['deliveryHooks'][$hookName]);
			foreach ($hooks as $identifier) {
			$functionName = OX_Delivery_Common_getFunctionFromComponentIdentifier($identifier, $hookName);
			if (function_exists($functionName)) {
			OX_Delivery_logMessage('calling on '.$functionName, 7);
			$return[$identifier] = call_user_func_array($functionName, $aParams);
			}
			}
			}
			}
			return $return;
}
function OX_Delivery_Common_getFunctionFromComponentIdentifier($identifier, $hook = null)////33
{
			$aInfo = explode(':', $identifier);
			$functionName = 'Plugin_' . implode('_', $aInfo) . '_Delivery' . (!empty($hook) ? '_' . $hook : '');
			if (!function_exists($functionName)) {
			if (!empty($GLOBALS['_MAX']['CONF']['pluginSettings']['useMergedFunctions'])) _includeDeliveryPluginFile('/var/cache/' . OX_getHostName() . '_mergedDeliveryFunctions.php');
			if (!function_exists($functionName)) {
			_includeDeliveryPluginFile($GLOBALS['_MAX']['CONF']['pluginPaths']['plugins'] . '/' . implode('/', $aInfo) . '.delivery.php');
			if (!function_exists($functionName)) {
			_includeDeliveryPluginFile('/lib/OX/Extension/' . $aInfo[0] . '/' . $aInfo[0] . 'Delivery.php');
			$functionName = 'Plugin_' . $aInfo[0] . '_delivery';
			if (!empty($hook) && function_exists($functionName . '_' . $hook)) {
			$functionName .= '_' . $hook;
			}
			}
			}
			}
			return $functionName;
}
function _includeDeliveryPluginFile($fileName)///34
{
			if (!in_array($fileName, array_keys($GLOBALS['_MAX']['FILES']))) {
			$GLOBALS['_MAX']['FILES'][$fileName] = true;
			if (file_exists(MAX_PATH . $fileName)) {
			include MAX_PATH . $fileName;
			}
			}
}
function OX_Delivery_logMessage($message, $priority = 6)////31
{
			$conf = $GLOBALS['_MAX']['CONF'];
			if (empty($conf['deliveryLog']['enabled'])) return true;
			$priorityLevel = is_numeric($conf['deliveryLog']['priority']) ? $conf['deliveryLog']['priority'] : 6;
			if ($priority > $priorityLevel && empty($_REQUEST[$conf['var']['trace']])) { return true; }
			error_log('[' . date('r') . "] {$conf['log']['ident']}-delivery-{$GLOBALS['_MAX']['thread_id']}: {$message}\n", 3, MAX_PATH . '/var/' . $conf['deliveryLog']['name']);
			OX_Delivery_Common_hook('logMessage', array($message, $priority));
			return true;
}


$file = '/lib/max/Delivery/cache.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
define ('OA_DELIVERY_CACHE_FUNCTION_ERROR', 'Function call returned an error');
$GLOBALS['OA_Delivery_Cache'] = array(
'prefix' => 'deliverycache_',
'host' => OX_getHostName(),
'expiry' => $GLOBALS['_MAX']['CONF']['delivery']['cacheExpire']
);

function OA_Delivery_Cache_fetch($name, $isHash = false, $expiryTime = null)/////38
{
			$filename = OA_Delivery_Cache_buildFileName($name, $isHash);
			$aCacheVar = OX_Delivery_Common_hook(
			'cacheRetrieve',
			array($filename),
			$GLOBALS['_MAX']['CONF']['delivery']['cacheStorePlugin']
			);
			if ($aCacheVar !== false) {
			if ($aCacheVar['cache_name'] != $name) {
			OX_Delivery_logMessage("Cache ERROR: {$name} != {$aCacheVar['cache_name']}", 7);
			return false;
			}
			if ($expiryTime === null) {
			$expiryTime = $GLOBALS['OA_Delivery_Cache']['expiry'];
			}
			$now = MAX_commonGetTimeNow();
			if ( (isset($aCacheVar['cache_time']) && $aCacheVar['cache_time'] + $expiryTime < $now)
			|| (isset($aCacheVar['cache_expire']) && $aCacheVar['cache_expire'] < $now) )
			{
			OA_Delivery_Cache_store($name, $aCacheVar['cache_contents'], $isHash);
			OX_Delivery_logMessage("Cache EXPIRED: {$name}", 7);
			return false;
			}
			OX_Delivery_logMessage("Cache HIT: {$name}", 7);
			return $aCacheVar['cache_contents'];
			}
			OX_Delivery_logMessage("Cache MISS {$name}", 7);
			return false;
}
function OA_Delivery_Cache_store($name, $cache, $isHash = false, $expireAt = null)
{
			if ($cache === OA_DELIVERY_CACHE_FUNCTION_ERROR) {
			return false;
}
$filename = OA_Delivery_Cache_buildFileName($name, $isHash);
$aCacheVar = array();
$aCacheVar['cache_contents'] = $cache;
$aCacheVar['cache_name'] = $name;
$aCacheVar['cache_time'] = MAX_commonGetTimeNow();
$aCacheVar['cache_expire'] = $expireAt;
return OX_Delivery_Common_hook(
'cacheStore',
array($filename, $aCacheVar),
$GLOBALS['_MAX']['CONF']['delivery']['cacheStorePlugin']
);
}
function OA_Delivery_Cache_store_return($name, $cache, $isHash = false, $expireAt = null)
{
			OX_Delivery_Common_hook(
			'preCacheStore_'.OA_Delivery_Cache_getHookName($name),
			array($name, &$cache)
			);
			if (OA_Delivery_Cache_store($name, $cache, $isHash, $expireAt)) {
			return $cache;
			}
			$currentCache = OA_Delivery_Cache_fetch($name, $isHash);
			if ($currentCache === false) {
			return $cache;
			}
			return $currentCache;
}
function OA_Delivery_Cache_getHookName($name)
{
			$pos = strpos($name, '^');
			return $pos ? substr($name, 0, $pos) : substr($name, 0, strpos($name, '@'));
}
function OA_Delivery_Cache_buildFileName($name, $isHash = false)//////39
{
			if(!$isHash) {
			$name = md5($name);
			}
			return $GLOBALS['OA_Delivery_Cache']['prefix'].$name.'.php';
}
function OA_Delivery_Cache_getName($functionName)///37
{
			$args = func_get_args();
			$args[0] = strtolower(str_replace('MAX_cacheGet', '', $args[0]));
			return join('^', $args).'@'.$GLOBALS['OA_Delivery_Cache']['host'];
}
function MAX_cacheGetAd($ad_id, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $ad_id);
			if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aRows = OA_Dal_Delivery_getAd($ad_id);
			$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
			}
			return $aRows;
}
function MAX_cacheGetAccountTZs($cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__);
			if (!$cached || ($aResult = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aResult = OA_Dal_Delivery_getAccountTZs();
			$aResult = OA_Delivery_Cache_store_return($sName, $aResult);
			}
			return $aResult;
}
function MAX_cacheGetZoneLinkedAds($zoneId, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $zoneId);
			if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aRows = OA_Dal_Delivery_getZoneLinkedAds($zoneId);
			$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
			}
			return $aRows;
}
function MAX_cacheGetZoneLinkedAdInfos($zoneId, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $zoneId);
			if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aRows = OA_Dal_Delivery_getZoneLinkedAdInfos($zoneId);
			$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
			}
			return $aRows;
}
function MAX_cacheGetZoneInfo($zoneId, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $zoneId);
			if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aRows = OA_Dal_Delivery_getZoneInfo($zoneId);
			$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
			}
			return $aRows;
}
function MAX_cacheGetLinkedAds($search, $campaignid, $laspart, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $search, $campaignid, $laspart);
			if (!$cached || ($aAds = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aAds = OA_Dal_Delivery_getLinkedAds($search, $campaignid, $laspart);
			$aAds = OA_Delivery_Cache_store_return($sName, $aAds);
			}
			return $aAds;
}
function MAX_cacheGetLinkedAdInfos($search, $campaignid, $laspart, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $search, $campaignid, $laspart);
			if (!$cached || ($aAds = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aAds = OA_Dal_Delivery_getLinkedAdInfos($search, $campaignid, $laspart);
			$aAds = OA_Delivery_Cache_store_return($sName, $aAds);
			}
			return $aAds;
}
function MAX_cacheGetCreative($filename, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $filename);
			if (!$cached || ($aCreative = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aCreative = OA_Dal_Delivery_getCreative($filename);
			$aCreative['contents'] = addslashes(serialize($aCreative['contents']));
			$aCreative = OA_Delivery_Cache_store_return($sName, $aCreative);
			}
			$aCreative['contents'] = unserialize(stripslashes($aCreative['contents']));
			return $aCreative;
}
function MAX_cacheGetTracker($trackerid, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $trackerid);
			if (!$cached || ($aTracker = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aTracker = OA_Dal_Delivery_getTracker($trackerid);
			$aTracker = OA_Delivery_Cache_store_return($sName, $aTracker);
			}
			return $aTracker;
}
function MAX_cacheGetTrackerLinkedCreatives($trackerid = null, $cached = true)////36
{

die("123");
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $trackerid);
			if (!$cached || ($aTracker = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aTracker = OA_Dal_Delivery_getTrackerLinkedCreatives($trackerid);
			$aTracker = OA_Delivery_Cache_store_return($sName, $aTracker);
			}
			return $aTracker;
}
function MAX_cacheGetTrackerVariables($trackerid, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $trackerid);
			if (!$cached || ($aVariables = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$aVariables = OA_Dal_Delivery_getTrackerVariables($trackerid);
			$aVariables = OA_Delivery_Cache_store_return($sName, $aVariables);
			}
			return $aVariables;
}
function MAX_cacheCheckIfMaintenanceShouldRun($cached = true)
{
			$interval = $GLOBALS['_MAX']['CONF']['maintenance']['operationInterval'] * 60;
			$delay = intval(($GLOBALS['_MAX']['CONF']['maintenance']['operationInterval'] / 12) * 60);
			$now = MAX_commonGetTimeNow();
			$today = strtotime(date('Y-m-d'), $now);
			$nextRunTime = $today + (floor(($now - $today) / $interval) + 1) * $interval + $delay;
			if ($nextRunTime - $now > $interval) {
			$nextRunTime -= $interval;
			}
			$cName = OA_Delivery_Cache_getName(__FUNCTION__);
			if (!$cached || ($lastRunTime = OA_Delivery_Cache_fetch($cName)) === false) {
			MAX_Dal_Delivery_Include();
			$lastRunTime = OA_Dal_Delivery_getMaintenanceInfo();
			if ($lastRunTime >= $nextRunTime - $delay) {
			$nextRunTime += $interval;
			}
			OA_Delivery_Cache_store($cName, $lastRunTime, false, $nextRunTime);
			}
			return $lastRunTime < $nextRunTime - $interval;
}
function MAX_cacheGetChannelLimitations($channelid, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $channelid);
			if (!$cached || ($limitations = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$limitations = OA_Dal_Delivery_getChannelLimitations($channelid);
			$limitations = OA_Delivery_Cache_store_return($sName, $limitations);
			}
			return $limitations;
}
function MAX_cacheGetGoogleJavaScript($cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__);
			if (!$cached || ($output = OA_Delivery_Cache_fetch($sName)) === false) {
			$file = '/lib/max/Delivery/google.php';
			if(!isset($GLOBALS['_MAX']['FILES'][$file])) {
			include MAX_PATH . $file;
			}
			$output = MAX_googleGetJavaScript();
			$output = OA_Delivery_Cache_store_return($sName, $output);
			}
			return $output;
}
function OA_cacheGetPublisherZones($affiliateid, $cached = true)
{
			$sName = OA_Delivery_Cache_getName(__FUNCTION__, $affiliateid);
			if (!$cached || ($output = OA_Delivery_Cache_fetch($sName)) === false) {
			MAX_Dal_Delivery_Include();
			$output = OA_Dal_Delivery_getPublisherZones($affiliateid);
			$output = OA_Delivery_Cache_store_return($sName, $output);
			}
			return $output;
}



OX_Delivery_logMessage('starting delivery script: ' . basename($_SERVER['REQUEST_URI']), 7);
if (!empty($_REQUEST[$conf['var']['trace']])) {
OX_Delivery_logMessage('trace enabled: ' . $_REQUEST[$conf['var']['trace']], 7);
}
MAX_remotehostSetInfo();
MAX_commonInitVariables();
MAX_cookieLoad();
MAX_cookieUnpackCapping();
if (empty($GLOBALS['_OA']['invocationType']) || $GLOBALS['_OA']['invocationType'] != 'xmlrpc')
{
OX_Delivery_Common_hook('postInit');
}


function MAX_javascriptToHTML($string, $varName, $output = true, $localScope = true)
{
		$jsLines = array();
		$search[] = "\\"; $replace[] = "\\\\";
		$search[] = "\r"; $replace[] = '';
		$search[] = '"'; $replace[] = '\"';
		$search[] = "'"; $replace[] = "\\'";
		$search[] = '<'; $replace[] = '<"+"';
		$string = str_replace($search, $replace, $string);
		$lines = explode("\n", $string);
		foreach ($lines AS $line) {
		if(trim($line) != '') {
		$jsLines[] = $varName . ' += "' . trim($line) . '\n";';
		}
		}
		$buffer = (($localScope) ? 'var ' : '') . $varName ." = '';\n";
		$buffer .= implode("\n", $jsLines);
		if ($output == true) {
		$buffer .= "\ndocument.write({$varName});\n";
		}
		return $buffer;
}
function MAX_javascriptEncodeJsonField($string)
{
		$string = addcslashes($string, "\\/\"\n\r\t");
		$string = str_replace("\x08", "\\b", $string);
		$string = str_replace("\x0C", "\\f", $string);
		return '"'.$string.'"';
}


MAX_commonSetNoCacheHeaders();
MAX_commonRegisterGlobalsArray(array('trackerid'));



$viewerId = MAX_cookieGetCookielessViewerID();

$transaction_id=$_REQUEST['transaction_id'];

$query_tranaction="select * from djax_s2s_track where transaction_id='$transaction_id '  and status!='1' ";

$res = OA_Dal_Delivery_query($query_tranaction);

$results =OA_Dal_Delivery_fetchAssoc($res);


//// Code by Digibrahma /////
	
include "s2s.php";
		

///////////////////////////////



if(empty($trackerid)) $trackerid = 0;

		$trackerid=$results['tracker_id'];

		if($conf['logging']['trackerImpressions'])//////40
		{



			if(OA_Dal_Delivery_numRows($res)>0)
			{		    		

						
						$aConversion = MAX_trackerCheckForValidAction($trackerid);

						if (!empty($aConversion))
						{
						$aConversionInfo = MAX_Delivery_log_logConversion($trackerid, $aConversion);
						
						$serverConvId = $serverRawIp = null;
						if (isset($aConversionInfo['deliveryLog:oxLogConversion:logConversion']['server_conv_id'])) {
						$serverConvId = $aConversionInfo['deliveryLog:oxLogConversion:logConversion']['server_conv_id'];
						}
						if (isset($aConversionInfo['deliveryLog:oxLogConversion:logConversion']['server_raw_ip'])) {
						$serverRawIp = $aConversionInfo['deliveryLog:oxLogConversion:logConversion']['server_raw_ip'];
						}
						$query=OA_Dal_Delivery_query("UPDATE  djax_s2s_track SET status=1 WHERE transaction_id='$transaction_id'");
						//MAX_Delivery_log_logVariableValues(MAX_cacheGetTrackerVariables($trackerid), $trackerid, $serverConvId, $serverRawIp);
						}
				
			}	
		}

MAX_cookieFlush();
MAX_commonDisplay1x1();
?>
