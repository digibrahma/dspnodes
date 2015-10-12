<?php
require_once '../../../../init.php';

require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dal/Delivery/mysql.php';


       $table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];



OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);


$title="DSP Portal list";

$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('dsp-list', $oHeaderModel);


// Prepare an array for storing error messages
$aErrormessage = array();
$a='http://'.$GLOBALS['conf']['webpath']['admin'].'/';

$deliverpath=$a.'plugins/DSP/dsp_request.php?dsp={place your dsp protal name here}';
?>


<div class="tableWrapper">
<h3><i>PING URL:<?php echo $deliverpath;?></i></h3>
    <div class="tableHeader">

        <ul class='tableActions'>
 	 
           <!-- <li class='inactive activeIfSelected'>
            
            	 <a id='deleteSelection'  href="" class='inlineIcon disable'>Delete</a>
                
                <script type='text/javascript'>
                <!--
        
                $('#deleteSelection').click(function(event) {
                    event.preventDefault();
                   
                    if (!$(this).parents('li').hasClass('inactive')) {
                        var ids = [];
                        $(this).parents('.tableWrapper').find('.toggleSelection input:checked').each(function() {
                            ids.push(this.value);
                        });
				window.location = 'oxm_ssp_action?action=delete&aid=' + ids.join(',');
					//var idv=ids.join(',');
						//var A='<?php //echo $a?>';
                        	//var new_href=A+'admin-report.php?action=reject&cid='+idv;
	                        //redirect1(new_href);
			 
                    }
					
                });
                </script>
                
            </li>-->
            
            
              <li class='inactive activeIfSelected'>
	
               <a id='enableSelection' href=""><img src="images/adxactive.png" width="15px" height="15px">&nbsp;UnBlock</a>   

		<script type='text/javascript'>
                <!--
        
                $('#enableSelection').click(function(event) {
                    event.preventDefault();
                   
                    if (!$(this).parents('li').hasClass('inactive')) {
                        var ids = [];
                        $(this).parents('.tableWrapper').find('.toggleSelection input:checked').each(function() {
                            ids.push(this.value);
                        });
                        window.location = 'oxm_dsp_action.php?action=enable&aid=' + ids.join(',');
				//var idv=ids.join(',');
				//var A='<?php// echo $a?>';
                        	//var new_href=A+'admin-report.php?action=accept&cid='+idv;
	                        //redirect(new_href);
					   				                   
                    }
                });
                </script>
		
  
          	             
            </li>
            
              <li class='inactive activeIfSelected'>
	
              <a id='disableSelection' href="" ><img src="images/adxdeactive.png" width="15px" height="15px">&nbsp;Block</a>    

		<script type='text/javascript'>
                <!--
        
                $('#disableSelection').click(function(event) {
                    event.preventDefault();
                   
                    if (!$(this).parents('li').hasClass('inactive')) {
                        var ids = [];
                        $(this).parents('.tableWrapper').find('.toggleSelection input:checked').each(function() {
                            ids.push(this.value);
                        });
                        window.location = 'oxm_dsp_action.php?action=disable&aid=' + ids.join(',');
				//var idv=ids.join(',');
				//var A='<?php// echo $a?>';
                        	//var new_href=A+'admin-report.php?action=accept&cid='+idv;
	                        //redirect(new_href);
					   				                   
                    }
                });
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
		
                <th>
                    <a href="#">DSP Portal Name</a>
                </th>
                
                                
                <th>
					<a href="#">Status</a>
                </th>
                
                <th >
                    <a href="#">Action</a>
                </th>

 	       <!--<th>  
		    <a href="#">Amount</a>
                </th>-->

 	       <th class="">  
                </th>

            </tr>
        </thead>

   <tbody>
<?php


$tempQuery= OA_Dal_Delivery_query("Select * FROM {$table_prefix}dj_dsp");
$i =1;
if(OA_Dal_Delivery_numRows($tempQuery)>0)
{
while($row=OA_Dal_Delivery_fetchAssoc($tempQuery))
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
                              <input type='checkbox' value="<?php print_r($row['id']);?>" />
                  </td>
		
		
	
 	<td>
		<a href="oxm_dsp_edit.php?aid=<?php echo $row['id'];?>" class='inlineIcon iconAdx'><?php print_r($row['dsp_portal_name']); ?></a>
	</td>
	
		
	<td>
		<?php if($row['status']=='1'){ ?>
			<a href="#" class='inlineIcon iconAdxactive'><img src="images/adxactive.png" width="15px" height="15px"></a>
		<?php }else{ ?>
			<a href="#" class='inlineIcon iconAdxdeactive'><img src="images/adxdeactive.png" width="15px" height="15px"></a>
		<?php } ?>
	</td>
 	
	<td>
		 <a  href="oxm_dsp_edit.php?aid=<?php echo $row['id'];?>&action=edit">Edit</a> 
			&nbsp; / &nbsp; 
	<!--	 <a  href="oxm_ssp_action.php?aid=<?php //print_r($row['exchange_id']);?>&action=delete">Delete</a>
			&nbsp; / &nbsp;-->
		<?php if($row['status']=='1'){ ?>	
			<a  href="oxm_dsp_action.php?aid=<?php print_r($row['id']);?>&action=disable">Block</a>
		<?php }else{ ?>
			<a  href="oxm_dsp_action.php?aid=<?php print_r($row['id']);?>&action=enable">UnBlock</a>
		<?php } ?>
		
	</td>
			 


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
                <td colspan='6'>&nbsp;</td>
            </tr>

            <tr class='even'>
                <td colspan='4' class="hasPanel">
                    <div class='tableMessage'>
                        <div class='panel'>
                                                            CURRENTLY THERE ARE NO DSP PORTAL AVAILABLE TO LIST
                            
                            <div class='corner top-left'></div>
                            <div class='corner top-right'></div>
                            <div class='corner bottom-left'></div>

                            <div class='corner bottom-right'></div>
                        </div>
                    </div>

                </td>
 			<td>&nbsp;</td>
            </tr>
            </tr>
            <tr class='odd'>
                <td colspan='4'>&nbsp;</td>
 <td colspan='4'>&nbsp;</td>
            </tr>


<?php
}
?>
</tbody>
</table></div>

<script type="text/javascript">
/*
GB_myShow = function(caption, url,  height, width, callback_fn) {

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
return GB_myShow('ACCEPT',url);
}

function redirect1(url)
{
return GB_myShow('REJECT',url);

}
*/

GB_myShow1 = function(caption, url, /* optional */ height, width, callback_fn) {

    var options = {
        caption: caption,
        height: height || 350,
        width: width || 605,
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



