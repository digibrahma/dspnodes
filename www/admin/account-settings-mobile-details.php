<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/


// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';

require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

$con = mysql_connect($GLOBALS['_MAX']['CONF']['database']['host'],$GLOBALS['_MAX']['CONF']['database']['username'],$GLOBALS['_MAX']['CONF']['database']['password']);
mysql_select_db($GLOBALS['_MAX']['CONF']['database']['name'], $con)or die("culnot select:".mysql_error());
$table_prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];


// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('mobile');

$prefSection = "mobile-details";

         
$setPref = $oOptions->getSettingsPreferences($prefSection);
$title = $setPref[$prefSection]['name'];


// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('mobile-details-index', $oHeaderModel);




  if(isset($_POST['add']))
        {
           if(isset($_POST['handset']))
             {

             	  mysql_query("Insert into oxm_handset (name) VALUES('".$_POST['name']."') ");
             }
           else if(isset($_POST['teleco']))
             {

             	  mysql_query("Insert into oxm_teleco (name) VALUES('".$_POST['name']."') ");
             }
           else if(isset($_POST['os']))
             {

             	  mysql_query("Insert into oxm_os (name) VALUES('".$_POST['name']."') ");
             }
	}

  if(isset($_POST['delete']))
     {
         if(isset($_POST['handset']))
             {

                  while (list($key, $val) = each($_POST['id1']))
                    {
                        mysql_query("Delete  from oxm_handset WHERE id='$val'") or die("this is already deleted.");

                     }

             }
          else if(isset($_POST['teleco']))
             {

                  while (list($key, $val) = each($_POST['id1']))
                    {
                        mysql_query("Delete  from oxm_teleco WHERE id='$val'") or die("this is already deleted.");

                     }

             }
          else if(isset($_POST['os']))
             {

                  while (list($key, $val) = each($_POST['id1']))
                    {
                        mysql_query("Delete  from oxm_os WHERE id='$val'") or die("this is already deleted.");

                     }

              }

    }
?>                      




<html>

      <head>
          
             <script type="text/javascript"> 
 function display(sat)
{

if(sat=="b1")
{



document.getElementById("div1").style.display="block";
document.getElementById("div2").style.display="none";
document.getElementById("div3").style.display="none";


}
else if(sat=="b2")
{


document.getElementById("div2").style.display="block";
document.getElementById("div1").style.display="none";
document.getElementById("div3").style.display="none";


}
else if(sat=="b3")
{


document.getElementById("div3").style.display="block";

document.getElementById("div2").style.display="none";
document.getElementById("div1").style.display="none";



}

}
                                      function validate(form)
                                           {
                                        var ser=form.name.value;
                                       if(ser.length==0)
                                        { 
                                          alert("u must enter the  name");
                                           return false;
                                        }
                                      return true;
                                            }
         </script>
 </head>


                                      <body>      
                                          <input type="button"  id="b1" value="Handset"  onclick="display(this.id)">
                                          <input type="button"  id="b2" value="Teleco"  onclick="display(this.id)">
                                                  
                                          <input type="button"  id="b3" value="OS"  onclick="display(this.id)">


                                                 <table border='0' width='100%' cellpadding='0' cellspacing='0'>
                                                         <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='1'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td></tr>	
						</table>

                          
      <div id="div1" style="display:none">
  <table border='0' width='100%' cellpadding='0' cellspacing='0'>

		 &nbsp;		
<h3><font size='4' color="#0FA54B"> Import Handset</font></h3></a>			
	
		 &nbsp;

                                <form enctype="multipart/form-data" action="account-settings-mobile-details.php" method="post" onsubmit="return validate(this);">                 
                                 
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                           <tr><td height='25' colspan='3'><h2>Add Products</h2></td></tr>

			                           
						            <tr><td style="font-size:12px;"><b>Name:</b></td>
							    <td><input name="name" type="text" size="52" /></td></tr>
						            <td><input type="hidden" name="handset" ></td>                                               <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
				                           <tr><td><input type="submit" name="add" value="add"></td></tr>

                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                              
                                                           </form>
                                                      <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='1'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                     </tr>
                                                   </table>
		                           <?php

	                                       $getdet = mysql_query("Select * from oxm_handset ") or die("no details");
				               if(mysql_num_rows($getdet)>0)
			                           { 
                                           ?>  

                                                         <form action="account-settings-mobile-details.php" method="post" >    
                                                         <table border='0' width='100%' cellpadding='0' cellspacing='0'>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                                       <tr><td height='25' colspan='3'><h2>Product Details</h2></td></tr>
                                                                       <tr><td><select name='id1[]' size=3 multiple>
                                                                     <?php
				                               while($getdetrow = mysql_fetch_array($getdet))

					                               {?>
								
				                         <option value="<?php echo $getdetrow['id']; ?>"><?php echo $getdetrow['name']; ?></option>
                               
				                                  <?php }
                                                    }?>
                                                                           </select></td></tr>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                         <td><input type="hidden" name="handset" ></td> 
                                                         <tr><td><input type="submit" name="delete" value="delete"></td></tr>

                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                         <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                        </tr>
	                                                    </table>

					          </form>
                                           </div>
 <div id="div2" style="display:none">
		 &nbsp;		
<h3><font size='4' color="#0FA54B"> Import Teleco</font></h3></a>		
		 &nbsp;

  <table border='0' width='100%' cellpadding='0' cellspacing='0'>
                                <form enctype="multipart/form-data" action="" method="post" onsubmit="return validate(this);">                 

                                   
	

                                                          <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                           <tr><td height='25' colspan='3'><h2>Add Products</h2></td></tr>
					                      					                           						                   <tr><td style="font-size:12px;"><b>Name:</b></td>
							   <td><input name="name" type="text" size="52" /></td></tr>
						           <td><input type="hidden" name="teleco" ></td> 															                                                                                                                                                                               <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

				                           <tr><td><input type="submit" name="add" value="add"></td></tr>

                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                              
                                                           </form>
                                                      <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='1'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                     </tr>
                                                   </table>
		                           <?php

	                                       $getdet = mysql_query("Select * from oxm_teleco ") or die("no details");
				               if(mysql_num_rows($getdet)>0)
			                           { 
                                           ?>  

                                                         <form action="" method="post" >    
                                                         <table border='0' width='100%' cellpadding='0' cellspacing='0'>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                                       <tr><td height='25' colspan='3'><h2>Product Details</h2></td></tr>
                                                                       <tr><td><select name='id1[]' size=3 multiple>
                                                                     <?php
				                               while($getdetrow = mysql_fetch_array($getdet))

					                               {?>
								
				                         <option value="<?php echo $getdetrow['id']; ?>"><?php echo $getdetrow['name']; ?></option>
                               
				                                  <?php }
                                                    }?>
                                                                           </select></td></tr>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                         <td><input type="hidden" name="teleco" > </td>
                                                         <tr><td><input type="submit" name="delete" value="delete"></td></tr>

                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                         <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                        </tr>
	                                                    </table>
					          </form>
                                           </div>
 <div id="div3" style="display:none">

		 &nbsp;		
<h3><font size='4' color="#0FA54B"> Import Hanset OS</font></h3></a>		
		 &nbsp;

  <table border='0' width='100%' cellpadding='0' cellspacing='0'>
                                <form enctype="multipart/form-data" action="" method="post" onsubmit="return validate(this);">          
                                                          <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                           <tr><td height='25' colspan='3'><h2>Add Products</h2></td></tr>
			                           
						            <tr><td style="font-size:12px;"><b>Name:</b></td>
							    <td><input name="name" type="text" size="52" /></td></tr>
						            <td><input type="hidden" name="os" ></td>  															                                                                                                                                                                               <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

				                           <tr><td><input type="submit" name="add" value="add"></td></tr>

                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                           <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                              
                                                           </form>
                                                      <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='1'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                     </tr>


                                                   </table>
		                           <?php

	                                       $getdet = mysql_query("Select * from oxm_os ") or die("no details");
				               if(mysql_num_rows($getdet)>0)
			                           { 
                                           ?>  

                                                         <form action="" method="post" >    
                                                         <table border='0' width='100%' cellpadding='0' cellspacing='0'>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                                       <tr><td height='25' colspan='3'><h2>Product Details</h2></td></tr>
                                                                       <tr><td><select name='id1[]' size=3 multiple>
                                                                     <?php
				                               while($getdetrow = mysql_fetch_array($getdet))

					                               {?>
								
				                         <option value="<?php echo $getdetrow['id']; ?>"><?php echo $getdetrow['name']; ?></option>
                               
				                                  <?php }
                                                    }?>
                                                                           </select></td></tr>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                         <td><input type="hidden" name="os" ></td> 

                                                         <tr><td><input type="submit" name="delete" value="delete"></td></tr>

                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>
                                                         <tr><td><img src="assets/images/spacer.gif" height="16" width="0" align="absmiddle"></td></tr>

                                                         <tr height='1'>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='250'></td>

                                                          <td width='30' bgcolor='#888888'><img src='assets/images/break.gif' height='1' width='30'></td>
                                                        </tr>
	                                                    </table>

					          </form>
                                           </div>

                                     </body></html>

                                                
<?
phpAds_PageFooter();

?>								
