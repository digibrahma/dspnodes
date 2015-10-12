<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| ==========                                                                |
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
$Id: account-settings-database.php 34688 2009-04-01 16:18:28Z andrew.hill $
*/

// Require the initialisation file
require_once '../../../../init.php';

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


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('IpCarrier');
$prefSection = "carrierupload";

// Prepare an array for storing error messages
$aErrormessage = array();



// Set the correct section of the settings pages and display the drop-down menu
$setPref = $oOptions->getSettingsPreferences($prefSection);
$title = $setPref[$prefSection]['name'];

// Display the settings page's header and sections
//$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('carrierupload', $title);


$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],
$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con);
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
$currentdir = dirname(__FILE__);



$csvpath = $currentdir."/csvUploads/";

if (isset($_POST['upload']))
{
	 // Advertisr File upload
	if ($_FILES["file1"]["error"] > 0)
   	{
    	//echo "Return Code: " . $_FILES["file1"]["error"] . "<br />";
   	}
  	else
   	{

		    mysql_query("delete from ".$table_prefix."excel");

		    move_uploaded_file($_FILES["file1"]["tmp_name"],$csvpath.$_FILES["file1"]["name"]);
			$row = 1;
			$handle = fopen($csvpath.$_FILES["file1"]["name"], "r");
			
			$countcarrier = 0;
			
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
			{
					$num = count($data);
					$row++;
					if($row>2)	{
					
						$start_ip=$data[0];
						$end_ip=$data[1];
						$country=$data[2];
						$carriername=$data[3];

			  if(!filter_var($start_ip, FILTER_VALIDATE_IP))
			  {
			  	continue;
			  }
			  else if(!filter_var($end_ip, FILTER_VALIDATE_IP))
			  {
			    	continue;
			  }
				


	if($data[0] == '' || $data[1] == '' || $data[2] == '' || $data[3] == '')
	{						
	mysql_query("INSERT INTO  ".$table_prefix."excel(start_ip,end_ip,country,carriername,Statustext)  VALUES('$start_ip','$end_ip','$country','$carriername','error')");
	}

						
	if($data[0] != '' && $data[1] != '' && $data[2] != '' &&$data[3] != '')
	{



		$djax_query = mysql_query("select * from ".$table_prefix."carrier_detail where start_ip = '".$start_ip."' and end_ip = '".$end_ip."'");
		$fetchrows=mysql_fetch_array($djax_query);
		
		if(mysql_num_rows($djax_query)>0)
		{
				mysql_query("update ".$table_prefix."carrier_detail set country='$country',carriername='$carriername' where start_ip = '".$start_ip."' and end_ip = '".$end_ip."'");
				
				if($fetchrows['carriername']==$carriername && $fetchrows['country']==$country && ($fetchrows['start_ip']!=$start_ip || $fetchrows['end_ip']!=$end_ip))
			{
				mysql_query("INSERT INTO  ".$table_prefix."carrier_detail(start_ip,end_ip,country,carriername) VALUES('$start_ip','$end_ip','$country','$carriername')");

				mysql_query("INSERT INTO  ".$table_prefix."excel(start_ip,end_ip,country,carriername,Statustext) 
												 VALUES('$start_ip','$end_ip','$country','$carriername','success')");
				
			}
			$countcarrier++;
		}
		else if(mysql_num_rows($djax_query)==0)
		{
			mysql_query("INSERT INTO  ".$table_prefix."carrier_detail(start_ip,end_ip,country,carriername) VALUES('$start_ip','$end_ip','$country','$carriername')");

				mysql_query("INSERT INTO  ".$table_prefix."excel(start_ip,end_ip,country,carriername,Statustext) 
												 VALUES('$start_ip','$end_ip','$country','$carriername','success')");
				$countcarrier++;

		}
		else
		{
			mysql_query("INSERT INTO  ".$table_prefix."excel(start_ip,end_ip,country,carriername,Statustext) 
										 VALUES('$start_ip','$end_ip','$country','$carriername','recored exisit')");
		}	

	

																		
	}						
						}
				}
				fclose($handle);		
			 ?>
				 	<div><font color="#009966" size="4"><li><?php echo $countcarrier;?> Carrier have been added/updated Sucessfully.</li></font></div>
		 <?php	
	}

 
}

$value=mysql_query("select * from ".$table_prefix."excel");
$num=mysql_num_rows($value);
if($num>0)
{
while($excel=mysql_fetch_row($value))
{
$val.=implode(',',$excel);
$val.='=>';
}

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
      <div class="select" style="text-align:left;">Select a file you would like to upload.</div>

      <div class="browse">
        <input type="text" id="picupload"  disabled="disabled" />

        <input type="file" name="file1" id="file1" value="" size="71"  onChange="document.getElementById(&quot;picupload&quot;).value=this.value"/> </div>
      <div class="allowed" style="text-align:left;">Csv Format only</div>
      <div style="display:none;" class="form_error" id="error_for_file"> &darr;&nbsp; &nbsp;&darr;</div>
      <div style="width:100%;text-align:left;	"> <input type="submit" name="upload" value="Upload" style="cursor:pointer;cursor:hand;" class="upload"  /> </div>

    </div>
    <div class="spacer"></div>
    <div class="bottom"></div>
    </form>
    
 <table  ><tr><td><form action="exel.php" method="post">
	<font size = '3px' color = '#056A9A'>Generate Excel Report of Last Upload details</font><input type="hidden" name="data" value="<?php echo $val;?>" ></input>  <input type="submit" value="Generate Excel" name="exl" style="background-color:#F7F7F7;color:#2D8800;-moz-border-radius:8px; border-color:#999999; font-weight:bolder;"   /> </form></td></tr></table> 
  </div>
	

&nbsp;

<br /><br />
<!--<a href="template.csv"><u>(click here to see the template file for uploading file contents)</u></a><br />--><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<!--<input type="file" name="from_ours" value="" multiple=""> -->



		
		
</body>
</html>
  
