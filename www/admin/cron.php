<?php
// Require the initialisation file
$path = dirname(__FILE__);
require_once $path . '/../../init.php';
function OX_Delivery_logMessage($message, $priority = 6) 
{
    $conf = $GLOBALS['_MAX']['CONF'];
    if (empty($conf['deliveryLog']['enabled'])) return true;
    $priorityLevel = is_numeric($conf['deliveryLog']['priority']) ? $conf['deliveryLog']['priority'] : 6;
    if ($priority > $priorityLevel && empty($_REQUEST[$conf['var']['trace']])) 
    {
        return true;
    }
    error_log('[' . date('r') . "] {$conf['log']['ident']}-delivery-{$GLOBALS['_MAX']['thread_id']}: {$message}\n", 3, MAX_PATH . '/var/' . $conf['deliveryLog']['name']);
    OX_Delivery_Common_hook('logMessage', array(
        $message,
        $priority
    ));
    return true;
}
function OX_Delivery_Common_hook($hookName, $aParams = array() , $functionName = '') 
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
            $hooks = explode('|', $GLOBALS['_MAX']['CONF']['deliveryHooks'][$hookName]);
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
    $aInfo = explode(':', $identifier);
    $functionName = 'Plugin_' . implode('_', $aInfo) . '_Delivery' . (!empty($hook) ? '_' . $hook : '');
    if (!function_exists($functionName)) 
    {
        if (!empty($GLOBALS['_MAX']['CONF']['pluginSettings']['useMergedFunctions'])) _includeDeliveryPluginFile('/var/cache/' . OX_getHostName() . '_mergedDeliveryFunctions.php');
        if (!function_exists($functionName)) 
        {
            _includeDeliveryPluginFile($GLOBALS['_MAX']['CONF']['pluginPaths']['plugins'] . '/' . implode('/', $aInfo) . '.delivery.php');
            if (!function_exists($functionName)) 
            {
                _includeDeliveryPluginFile('/lib/OX/Extension/' . $aInfo[0] . '/' . $aInfo[0] . 'Delivery.php');
                $functionName = 'Plugin_' . $aInfo[0] . '_delivery';
                if (!empty($hook) && function_exists($functionName . '_' . $hook)) 
                {
                    $functionName.= '_' . $hook;
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
function MAX_Dal_Delivery_Include() 
{
    static $included;
    if (isset($included)) 
    {
        return;
    }
    $included = true;
    $conf = $GLOBALS['_MAX']['CONF'];
    if (isset($conf['origin']['type']) && is_readable(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php')) 
    {
        require (MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php');
    }
    else
    {
        require (MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['database']['type']) . '.php');
    }
}
MAX_Dal_Delivery_Include();
if (!empty($conf['debug']['production']))
{
	error_reporting(E_ALL & ~(E_NOTICE | E_WARNING | E_DEPRECATED | E_STRICT));
}
else
{
	error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT));
}
//Setting up for unlimited time.
set_time_limit (0);

$start=date('Y-m-d 00:00:00', strtotime('Yesterday'));
$end=date('Y-m-d 23:00:00', strtotime('Yesterday'));

backup_db($start,$end);

/// move data to bkp table from request
requestBck($start,$end);

/// move data to bkp table from response
responseBck($start,$end);

/// move data to bkp table from winnotice
winnoticeBck($start,$end);
    
function backup_db($start,$end)
{
	$return='';
	$table='djax_additional_banners';
	//foreach($allTables as $table){

	$result = mysql_query("SELECT * FROM ".$table." limit 1");
	$num_fields = mysql_num_fields($result);

	$return.= 'DROP TABLE IF EXISTS '.$table.';';
	$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
	$return.= "\n\n".$row2[1].";\n\n";

	for ($i = 0; $i < $num_fields; $i++) {
		 $k=0;
	while($row = mysql_fetch_row($result)){ 
		if($k==0)
		{
			//echo "delete  FROM ".$table." where image_id=".$row[0];
			//mysql_query("delete  FROM ".$table." where image_id=".$row[0]);
			$k==1;
		}
	   $return.= 'INSERT INTO '.$table.' VALUES(';
		 for($j=0; $j<$num_fields; $j++){
		   $row[$j] = addslashes($row[$j]);
		   $row[$j] = str_replace("\n","\\n",$row[$j]);
		   if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } 
		   else { $return.= '""'; }
		   if ($j<($num_fields-1)) { $return.= ','; }
		 }
	   $return.= ");\n";
	}
	}
	$return.="\n\n";
	//}

	// Create Backup Folder
	$folder = '/tmp/';
	if (!is_dir($folder))
	//mkdir($folder, 0777, true);
	chmod($folder, 0777);

	$date = date('m-d-Y-H-i-s', time()); 
	$filename = $folder."db-backup-".$date; 

	$handle = fopen($filename.'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}

?>

