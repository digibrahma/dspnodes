<?php

// Require the initialisation file
$path = dirname(__FILE__);
require_once $path . '/../../init.php';

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con) or die(mysql_error());

  
//Setting up for unlimited time.
set_time_limit (0);

/*
echo $start=date('Y-m-d 00:00:00', strtotime('Yesterday'));
echo $end=date('Y-m-d 23:00:00', strtotime('Yesterday'));
*/

$start=date("Y-m-d", time() - 230400);
$end=date("Y-m-d", time() - 172800);


//backup_db($start,$end);

/// move data to bkp table from request
if(requestBck($start,$end))
{
	echo "test";
	
}

/// move data to bkp table from response
responseBck($start,$end);

/// move data to bkp table from winnotice
winnoticeBck($start,$end);
    
function backup_db($start,$end)
{
	$return='';
	$table='rv_banners';
	
	//foreach($allTables as $table){

	$result = mysql_query("SELECT * FROM ".$table."");
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
	$folder = '/var/tmp/';
	if (!is_dir($folder))
	mkdir($folder, 0777, true);
	chmod($folder, 0777);

	$date = date('m-d-Y', time()); 
	$filename = $folder."db-backup-".$date; 

	if (!is_writable($filename)) { // Test if the file is writable
    echo "Cannot write to {$filename}";
    //zexit;
	}
	$handle = fopen($filename.'.sql','w') or die(print_r(error_get_last()));
	if(fwrite($handle,$return))
	{
		echo 'fine';
	}
	fclose($handle);
}

function requestBck($start,$end)
{
	
	if($result = mysql_query("call requestBck('$start','$end')"))
	{
		return true;
	}
	
	
	
}

function responseBck($start,$end)
{
	
	if($result = mysql_query("call responseBck('$start','$end')"))
	{
		return true;
	}
	
	
	
}

function winnoticeBck($start,$end)
{
	
	if($result = mysql_query("call winnoticeBck('$start','$end')"))
	{
		return true;
	}

	
}


?>

