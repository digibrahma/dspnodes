<?php

set_time_limit(1000);
ob_start();

// Require the initialisation file
require_once '../../init.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';
// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';


// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dll.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

require_once LIB_PATH . '/Plugin/Component.php';

OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);


phpAds_PageHeader('ipupload-settings-index', $oHeaderModel);
$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
$oOptions = new OA_Admin_Option('user');
$currentdir = dirname(__FILE__);



 $csvpath = $currentdir."/csvUploads/";

if (isset($_POST['upload']))
{
	$operator_name=$_POST['operator'];
	 // Advertisr File upload
	if ($_FILES["file1"]["error"] > 0)
   	{
    	//echo "Return Code: " . $_FILES["file1"]["error"] . "<br />";
   	}
  	else
   	{

		   
		    move_uploaded_file($_FILES["file1"]["tmp_name"],$csvpath.$_FILES["file1"]["name"]) or die('not Upload');
			$row = 1;
			$handle = fopen($csvpath.$_FILES["file1"]["name"], "r");
			
			$countcarrier = 0;
			
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
			{		
					echo '<pre>';
					print_r($data);
					$num = count($data);
					$row++;
					
					//if($row>2)	{
					$ip_address=$data[0];	
					$ip_address_array=renderIPRange($data[0]);				
					if($ip_address_array!="invalid"){
						
	$ip_address=$ip_address_array["original"];
		
					
					
	if($data[0] != '')
	{
		
		$djax_query = OA_Dal_Delivery_query("select * from djax_iprange where ipaddress='".$ip_address."'");
		$fetchrows=OA_Dal_Delivery_fetchAssoc($djax_query);
		
		if(mysql_num_rows($djax_query)==0)
		{
			if($countcarrier==0){
				 OA_Dal_Delivery_query("truncate table ".$table_prefix."excel");

				}
			
				$countcarrier++;
		OA_Dal_Delivery_query("insert into djax_iprange(name,ipaddress,hostmin,hostmax) values('".$operator_name."','".$ip_address."','".$ip_address_array["1st"]."','".$ip_address_array["2nd"]."')");
		
		OA_Dal_Delivery_query("INSERT INTO  ".$table_prefix."excel(name,ipaddress,hostmin,hostmax) 
			 VALUES('".$operator_name."' ,'".$ip_address."','".$ip_address_array["1st"]."','".$ip_address_array["2nd"]."')");
		}
		
			


																		
	}						
	}
	
				
	//}
	
			}
				fclose($handle);		
			 ?>
				 	<div><font color="#009966" size="4"><li><?php echo $countcarrier;?> Ip Ranges have been added/updated Sucessfully.</li></font></div>
		 <?php	
	}

 
}

$value=OA_Dal_Delivery_query("select name,ipaddress,hostmin,hostmax from ".$table_prefix."excel");
$num=mysql_num_rows($value);
if($num>0)
{
while($excel=mysql_fetch_row($value))
{
$val.=implode(',',$excel);
$val.='=>';
}

}


 function cidr2netmask($cidr)
    {
		$bin="";
        for( $i = 1; $i <= 32; $i++ )
        $bin .= $cidr >= $i ? '1' : '0';

        $netmask = long2ip(bindec($bin));

        if ( $netmask == "0.0.0.0")
        return false;

    return $netmask;
    }
function mask2cidr($mask){
	$long = ip2long($mask);
	$base = ip2long('255.255.255.255');
	return 32-log(($long ^ $base)+1,2);

	
}

function renderIPRange($inp){
	$startingIp="";
	$endingIp="";

$input=$inp;
// Parsing the Input
$positionofSplit=strrpos($input, "/");
$ipAddress=substr($input, 0, $positionofSplit);
$suffix=substr($input, ($positionofSplit+1), strlen($input));
$suffix=(int)$suffix;
$validation_ip=validateIP($ipAddress);
if($validation_ip!="0"){
	$ipAddress=$validation_ip;
		if($suffix>32){
	// Follows no.of.Connection
	$originalIp=$ipAddress; // Assign First IP
$fixIplen=$suffix;

// IP to Long
$originalIpVal=sprintf('%u', ip2long($originalIp));
$originalIpVal++;
$endIpVal=($originalIpVal+$fixIplen)-1;

// Long to Ip
$startingIp=long2ip($originalIpVal);
$endingIp=long2ip($endIpVal);
	
	}
	else{
		
		$startIp=sprintf('%u', ip2long($ipAddress));
		// Follows Subnet Masking
		$sub=cidr2netmask($suffix);
		$subnet=mask2cidr($sub);

$temp=pow(2,(32-$subnet));

$temp=$temp-2;

//	echo $startIp."<br>";
	$startIp=$startIp+$temp;
	//echo $startIp."<br>";
	$startingIp=$ipAddress;
	$endingIp=long2ip($startIp);
		
		}
		
		
$return_ip_info=array("original" => $ipAddress."/".$suffix,"1st" => $startingIp,"2nd"=>$endingIp);

return $return_ip_info;

	}
	else{
		return "invalid";
		}
	

	}
function validateIP($inp){
	$count=0;
	$formatedIp=0;
	$pieces = explode(".", $inp);
	for($i=0;$i<count($pieces);$i++){
		if($i==0){
		$formatedIp=(int)$pieces[$i];	
			}
		else{
				$formatedIp=$formatedIp.".".(int)$pieces[$i];
				}
		
		$count++;
		}
if($count==4){
	return $formatedIp;
	}
	else return "0";
	}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/upload-media.css"/>

</head>
<body>
<script type="text/javascript">
 
function loadImages() {

	document.getElementById('loading_images').style.display = "block"; 

  }
  
function check(){
var res = document.getElementById('file1').value;
if(res == ''){

alert('please Select  file first');
return false;
}
var a=document.getElementById("file1").value.toLowerCase();

if(a!='')
{
var c=a.split('.');
}
if(c[1]!='csv')
{

alert('please Select  CSV file format only ');

return false;
}

}
</script>

<form enctype="multipart/form-data" action="" method="POST" onsubmit ="return check()" name="myForm">
<input type="hidden" name="20" value="100000" />
    <div class="content">
      <div class="mediacount">

      </div><br><br><br><br>
      <div id="loading_images" style="display:none; margin-left:-350px;padding-bottom:15px; padding-top:15px;"> </div>
     <div class="select" style="text-align:left; padding-bottom:3px;"><label>Enter the Operator name</label></div>
      <div class="select" style="text-align:left;"><input type="text" name="operator" placeholder="operator"/></div>
      </br>
<div class="select" style="text-align:left; padding-bottom:3px;">Select a file you would like to upload.</div>
      <div class="browse">
        <input type="text" id="picupload"  disabled="disabled" />

        <input type="file" name="file1" id="file1" value="" size="71"  onChange="document.getElementById(&quot;picupload&quot;).value=this.value"/> </div>
        </br>
      <div class="allowed" style="text-align:left; padding-bottom:3px;">Csv Format only</div>
      <div style="display:none;" class="form_error" id="error_for_file"> &darr;&nbsp; &nbsp;&darr;</div>
      <div style="width:100%;text-align:left;	"> <input type="submit" name="upload" value="Upload" style="cursor:pointer;cursor:hand;" class="upload"  /> </div>

    </div>
    <div class="spacer"></div>
    <div class="bottom"></div>
    </form>
    
 <table  ><tr><td></td></br><form action="exel.php" method="post">
	<font size = '3px' color = '#056A9A'>Generate Excel Report of Last Upload details</font><input type="hidden" name="data" value="<?php echo $val;?>" ></input>  <input type="submit" value="Generate Excel" name="exl" style="background-color:#F7F7F7;color:#2D8800;-moz-border-radius:8px; border-color:#999999; font-weight:bolder;"   /> </form></td></tr></table> 
  </div>
	

&nbsp;

<br /><br />
<!--<a href="template.csv"><u>(click here to see the template file for uploading file contents)</u></a><br />--><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<!--<input type="file" name="from_ours" value="" multiple=""> -->



		
		
</body>
</html>
  
