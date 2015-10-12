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
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/component/Form.php';


// Required files

require_once MAX_PATH . '/lib/OA/Dll.php';

require_once MAX_PATH . '/lib/OX/Admin/UI/ViewHooks.php';

require_once LIB_PATH . '/Plugin/Component.php';
require_once MAX_PATH . '/lib/OA/Admin/TemplatePlugin.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


phpAds_PageHeader("carrierdetail", '');


 ?>

<div class="tableWrapper">
    <div class="tableHeader">

     <ul class='tableActions'>
 	   <li class='inactive activeIfSelected'>
	


		<script type='text/javascript'>
                <!--
        
                $('#approveSelection').click(function(event) {
                    event.preventDefault();
                    
                    if (!$(this).parents('li').hasClass('inactive')) {
                        var ids = [];
                        $(this).parents('.tableWrapper').find('.toggleSelection input:checked').each(function() {
                            ids.push(this.value);
                        });
                        if (!tablePreferences.warningBeforeDelete || confirm("Do You Really Want To Delete Selected Carrier?")) {

                            window.location = 'carrier-delete.php?id=' + ids.join(',');
                        }
                    }
                });
                
                //-->
                </script>
		
  
          	             
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
                        if (!tablePreferences.warningBeforeDelete || confirm("Do You Really Want To Delete Selected carriers?")) {

                            window.location = 'carrier-delete.php?id=' + ids.join(',');
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
                <th class="">
                    <a href="#">Name</a>
                </th>
                <th class="">
                    <a href="#">Country</a>
                </th>
                <th class="">
                    <a href="#">Start IP Address</a>
                </th>
                
                <th class="">
                    <a href="#"> End IP Address</a>
                </th>

 	       <th class="">  
                </th>

            </tr>
        </thead>

   <tbody>

<?php 

$query = mysql_query("select * from  ".$table_prefix."carrier_detail");

if(mysql_num_rows($query) > 0){

$i =1;
while($row = mysql_fetch_assoc($query))
{

	if($i == 1) {

		$_class = "odd hilite";

	} elseif($i%2 == 0) {

		$_class = "even";

	} else {

		$_class = "odd";

	} ?>

	<tr class="<?php echo $_class; ?>">

	           <td class='toggleSelection'>
                              <input type='checkbox' value="<?=$row['id']?>" />
                   </td>

                  <td> <a href="carrier-edit.php?id=<?php echo $row['id']; ?>"><?php echo $row['carriername']; ?></a></td>
                  
		  <td><?php echo $row['country']; ?></td>	
		 		  	
                  <td><?php echo $row['start_ip']; ?></td>
		
                  <td><?php echo $row['end_ip']; ?></td>
		
     		 <td class="alignRight verticalActions">
                    <ul class="rowActions"> <li></li></ul></td>
        </tr>
<?php 

	$i++;
} 

} else { ?>

<tr class='odd'>
                <td colspan='6'>&nbsp;</td>
            </tr>
            <tr class='even'>

                <td colspan='6' class="hasPanel">
                    <div class='tableMessage'>
                        <div class='panel'>

                            There are currently no Carriers to display.
                                                            
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






<?php } ?>


</tbody>
</table></div>

<?php
phpAds_PageFooter();

?>
  
