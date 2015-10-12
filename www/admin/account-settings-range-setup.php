<?php



require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/config.php';
require_once LIB_PATH . '/Plugin/PluginManager.php';
require_once LIB_PATH . '/Plugin/ComponentGroupManager.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

$oPluginManager = new OX_PluginManager();
$oComponentGroupManager = new OX_Plugin_ComponentGroupManager();
phpAds_PageHeader('iprange-settings-index', $oHeaderModel);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:"."Error1--->".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

if($_GET['orderdirection']=='down')
{
$orderdirection="up";

 $session['prefs']['account-settings-range-setup.php']['orderdirection']="up";
}
else if($_GET['orderdirection']=='up')
{
$orderdirection="down";

 $session['prefs']['account-settings-range-setup.php']['orderdirection']="down";
}
else
{
	if(empty($session['prefs']['account-settings-location-setup.php']['orderdirection']))
	{
	$orderdirection="down";	

	}
	else
	{
	$orderdirection=$session['prefs']['account-settings-location-setup.php']['orderdirection'];
	}
}


if(!empty($_GET['listorder']))
{
$listorder=$_GET['listorder'];
$session['prefs']['account-settings-location-setup.php']['listorder']=$_GET['listorder'];
}
else
{
	if(empty($session['prefs']['account-settings-location-setup.php']['listorder']))
	{
	$listorder='name';
	}
	else
	{
	$listorder=$session['prefs']['account-settings-location-setup.php']['listorder'];
	}

}


phpAds_SessionDataStore();

if($orderdirection=='up')
{
$class1='sortDown';
}
else if($orderdirection=='down')
{
$class1='sortUp';
}


?>

<div class="tableWrapper">
    <div class="tableHeader">

        <ul class='tableActions'>
 	   <li >
		
	
               <a id='approveSelection' href='oxm_createrange.php' class='inlineIcon iconTargetingChannelAdd'>Add New</a>    
		
		         	             	
            </li>
            <li class='inactive activeIfSelected'>
            
            	
                <a id='deleteSelection' href='#' class='inlineIcon iconDelete'>Delete</a>
               

                <script type='text/javascript'>
                <!--
        
                $('#deleteSelection').click(function(event) {
                    event.preventDefault();
                    
                    if (!$(this).parents('li').hasClass('inactive')) {
                        var ids = [];
                        $(this).parents('.tableWrapper').find('.toggleSelection input:checked').each(function() {
                            ids.push(this.value);
                        });
                        if (!tablePreferences.warningBeforeDelete || confirm("Do You Really Want To Delete Selected Direct Links?")) {

                            window.location = 'oxm_deleterange.php?id=' + ids.join(',');
                        }
                    }
                });
                
                //-->
                </script>
                
            </li>
        </ul>


        <div class="clear"></div>

        <div class="corner left"></div>
        <div class="corner right"></div>
    </div>

    <table cellspacing="0" summary="">
        <thead>
            <tr>

               <th class='first toggleAll'>
                  <input type='checkbox' />
                </th>
		
		<th  class="<?php echo $class1;?>">
                    <a href="account-settings-range-setup.php?listorder=name&orderdirection=<?php echo $orderdirection;?>">Location Name</a>
                </th>
		 <th class="<?php echo $class1;?>">
                     <a href="account-settings-range-setup.php?listorder=hostmin&orderdirection=<?php echo $orderdirection;?>">IP Pool</a>
                </th>
		<th class="<?php echo $class1;?>">
                     <a href="account-settings-range-setup.php?listorder=hostmin&orderdirection=<?php echo $orderdirection;?>">HostMin</a>
                </th>
		<th class="<?php echo $class1;?>">
                     <a href="account-settings-range-setup.php?listorder=hostmax&orderdirection=<?php echo $orderdirection;?>">HostMax</a>
                </th>
		
            </tr>
        </thead>

   <tbody>

<?php 


if($orderdirection=='down')
{
$order='asc';
}
else if($orderdirection=='up')
{
$order='desc';
}
$tempQuery= mysql_query("Select * from djax_iprange order by $listorder $order");



//dac015//
$i =1;
if(mysql_num_rows($tempQuery)>0)
{
while($row=mysql_fetch_assoc($tempQuery))
{

if($i == 1) {

		$_class = "odd hilite";

	} elseif($i%2 == 0) {

		$_class = "even";

	} else {

		$_class = "odd";

	}

?>
	<tr class="<?php echo $_class; ?>">

	        <td class='toggleSelection'>
			<input type='checkbox' value="<?php echo $row['locid'];?>" />
		</td>
		<td>
		<a href="oxm_createrange.php?id=<?php echo $row['locid'];?>"><? echo $row['name'];?></a>
		</td>
		<td>
		<?php echo $row['ipaddress'];?>
		</td>
		<td>
		<?php echo $row['hostmin'];?>
		</td>
		<td>
		<?php echo $row['hostmax'];?>
		</td>	

        </tr>
<?php 


	$i++;
}
}
else
{
?>
  <tr class='odd'>
                <td colspan='6'>&nbsp;</td>
            </tr>

            <tr class='even'>
                <td colspan='6' class="hasPanel">
                    <div class='tableMessage'>
                        <div class='panel'>
                     CURRENTLY THERE ARE NO AKA NETWORK GEODATA  TO DISPLAY
                            
                            <div class='corner top-left'></div>
                            <div class='corner top-right'></div>
                            <div class='corner bottom-left'></div>

                            <div class='corner bottom-right'></div>
                        </div>
                    </div>

                    &nbsp;
                </td>
            </tr>
            <tr class='odd'>
                <td colspan='6'>&nbsp;</td>

            </tr>


<?php
}
?>
</tbody>
</table></div>


<?php
phpAds_PageFooter();

?>

