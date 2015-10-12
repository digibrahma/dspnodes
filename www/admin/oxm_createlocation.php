<?php
ob_start();
// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once MAX_PATH . '/lib/OA/Creative/File.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';


// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';

require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';

require_once MAX_PATH . '/lib/max/Admin_DA.php';


OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);


phpAds_PageHeader('iplocation-settings-index', $oHeaderModel);
$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
$oOptions = new OA_Admin_Option('user');

$aErrormessage = array();
$translation = new OX_Translation ();

	if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') 
	{
		$count=mysql_num_rows(mysql_query("Select * from rv_akanetwork_cities where locid='".$_POST['id']."'"));

		
			$aBanner= mysql_fetch_assoc(mysql_query("Select * from rv_banners as b,rv_campaigns as c, rv_clients as a where 
			 b.campaignid=c.campaignid and c.clientid=a.clientid and b.bannerid={$_POST['banner']}"));
			$azone= mysql_fetch_assoc(mysql_query("Select * from rv_zones where zoneid={$_POST['zone']}"));

				

					if($_POST['id']=='')
					{
		   				mysql_query("insert into ox_akanetwork_cities (location_name,latitude,longitude,Diameter) values 
						('".$_POST['name']."','".$_POST['lat']."','".$_POST['long']."','".$_POST['diameter']."')") ;
						$translated_message = $translation->translate ("Your Location hase been created successfully");
				   		OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
						OX_Admin_Redirect::redirect("account-settings-location-setup.php");
					}
					else
					{
						mysql_query("update ox_akanetwork_cities set location_name='{$_POST['name']}',
						latitude='".$_POST['lat']."',longitude='".$_POST['long']."',Diameter='".$_POST['diameter']."' where locid='".$_POST['id']."'");
				  		$translated_message = $translation->translate ("Your Location  hase been Updated successfully");
				   		OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
						OX_Admin_Redirect::redirect("oxm_createlocation.php?id={$_POST['id']}");

					}
				
				
		
		

	}

$title='Create IP Location';
// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
?>



<style type="text/css"/>
#clientid
{
display:none;
}
</style>
<?php

if(!empty($_REQUEST['id']))
{
$select = mysql_query("Select * from ox_akanetwork_cities where locid='".$_REQUEST['id']."'") or die("error");
$row = mysql_fetch_array($select);
if(mysql_num_rows($select) > 0) {
$name=$row['location_name'];
$diameter=$row['Diameter'];
$long=$row['longitude'];
$lat=$row['latitude'];
$id=$row['locid'];
} 
}



$aSettings = array(

 array (
        'text'    => 'IP LOCATION GENERATOR',
        'items'   => array (
				array  (
					'type'    => 'text',
					'name'    => 'name',
					'text'    => 'Location Name<span style="color:red;">*</span>  ',
					'size'    => 50,
					'value'	  => $name
				        ),
				 array (
					'type'    => 'break'				
		
				    ),

				 array  (
					'type'    => 'text',
					'name'    => 'lat',
					'text'    => 'Latitude<span style="color:red;">*</span>  ',
					'size'    => 50,
					'value'	  => $lat	
				        ),
				 array (
					'type'    => 'break'				
		
				    ),
	  			array (
		                    'type'    => 'hiddenfield',
		                    'name'    => 'id',
				    'value'   => $id
            			),
				    array (
					'type'    => 'text',
					'name'    => 'long',
					'text'    => 'Longitude <span style="color:red;">*</span> ',
					'size'    => 50,
					'value'	  => $long
				        ),
				 array (
					'type'    => 'break'				
		
				    ),
				    array (
					'type'    => 'text',
					'name'    => 'diameter',
					'text'    => 'R(Radius) <span style="color:red;">*</span> ',
					'size'    => 50,
					'value'	  => $diameter,
				        ),
				 array (
					'type'    => 'break'				
		
				    ),
			
			  )
	 )
  

);


$oOptions->show($aSettings, null);
phpAds_PageFooter();
ob_flush();
?>
<script type="text/javascript">
  function hide() 
  {
   document.getElementById("divDyna").style.display="none";
  }

 function DynamicDiv(a)
 {      
        var dynDiv = document.createElement("div");
        dynDiv.id = "divDyna";
        dynDiv.innerHTML = a;
        dynDiv.style.left = "300px";
	dynDiv.style.top = "100px";
	dynDiv.style.position = "fixed";
        dynDiv.style.height = "140px";
        dynDiv.style.width = "400px"; 
	dynDiv.style.color = "white";  
	dynDiv.style.fontSize = "20px";    
	dynDiv.style.fontWidth = "bold";  
        dynDiv.style.zIndex = "100000000"; 
        dynDiv.style.backgroundColor = '#F95F54';
	dynDiv.style.border = "4px solid #000";
        document.body.appendChild(dynDiv);
	setTimeout('hide()',1000);
 }
 function isUrl(s) 
 {
	var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(s);
 }
function max_formValidate()
{
	var error='';
	if(document.getElementById("name").value=='')
	{
		document.getElementById("name").style.border="1px solid red";
		error+="Enter the Location Name";

	}
	if(document.getElementById("lat").value=='')
	{
		document.getElementById("lat").style.border="1px solid red";
		if(error!=''){error+='<br>';}
		error+="Enter the Lattitude";

	}
	if(document.getElementById("long").value=='')
	{
		document.getElementById("long").style.border="1px solid red";
		if(error!=''){error+='<br>';}
		error+="Enter the Longitude";

	}
	else if(document.getElementById("diameter").value=='')
	{
		document.getElementById("diameter").style.border="1px solid red";
		error+="Enter the Radius";
	}
	
	if(error!="")
	{

		DynamicDiv(error);
		return false;
	}else
	{
		hide();
	}
}
	</script>
			
	
