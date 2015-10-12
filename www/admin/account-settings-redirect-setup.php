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
phpAds_PageHeader('redirection-settings-index', $oHeaderModel);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:"."Error1--->".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];

$aErrormessage = array();
$a='http://'.$GLOBALS['conf']['webpath']['admin'].'/';
?>


<div class="tableWrapper">
    <div class="tableHeader">

        <div class="clear"></div>

        <div class="corner left"></div>
        <div class="corner right"></div>
    </div>

    <table cellspacing="0" summary="">
        <thead>
            <tr>

                <th class="">
                    <a href="#">Publisher Name</a>
                </th>
                
                <th >
                    <a href="#">Redirect Url</a>
                </th>

            </tr>
        </thead>

   <tbody>
<?php
$tempQuery= mysql_query("Select * from {$table_prefix}affiliates");
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
		
		
		
 			<td>
				<a href="redirecturl.php?affiliateid=<?php print_r($row['affiliateid']);?>" onclick="return GB_myShow1('Puublisher Details', this.href)" class='inlineIcon iconWebsite'>
				<?php print_r($row['name']); ?></a>
				
			</td>
 
<?php if($row['redirecturl']!=""){?>
			 <td><?php print_r($row['redirecturl']); ?></td>
	
		<?php }else{          ?>
 		<td><center>-</td>

<?php } ?>
	<td class="alignRight verticalActions">
                    <ul class="rowActions"> <li></li></ul></td>

        </tr>
<?php 


	$i++;
}
}
else
{
?>
  <tr class='odd'>
                <td colspan='4'>&nbsp;</td>
            </tr>

            <tr class='even'>
                <td colspan='4' class="hasPanel">
                    <div class='tableMessage'>
                        <div class='panel'>
                       CURRENTLY THERE ARE NO PUBLISHER AVAILABLE
                            
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
                <td colspan='4'>&nbsp;</td>

            </tr>


<?php
}
?>
</tbody>
</table></div>

<script type="text/javascript">
GB_myShow = function(caption, url, /* optional */ height, width, callback_fn) {

    var options = {
        caption: caption,
        height: height || 350,
        width: width || 380,
        fullscreen: false,
        show_loading: false,
        callback_fn: callback_fn
    }
    var win = new GB_Window(options);
    return win.show(url);
}

function redirect(url)
{
return GB_myShow('ACTIVATE',url);
}

function redirect1(url)
{
return GB_myShow('DEACTIVATE',url);
}

GB_myShow1 = function(caption, url, /* optional */ height, width, callback_fn) {

    var options = {
        caption: caption,
        height: height || 200,
        width: width || 600,
        fullscreen: false,
        show_loading: false,
        callback_fn: callback_fn
    }
    var win = new GB_Window(options);
    return win.show(url);
}

</script>


<?php

phpAds_PageFooter();

?>



