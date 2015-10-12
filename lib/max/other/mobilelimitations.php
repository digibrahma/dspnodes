<?php

require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/max/Dal/Admin/Acls.php';

function getMobileLimitations($aAcls)
{

    if (empty($aAcls))
    {
        return "true";
    }
    else
    {
        ksort($aAcls);
        $compiledAcls = array();
	//$aclData = array();

        foreach ($aAcls as $acl)
        {
	    if(count($acl['data']) > 1)
		{
	  		  $aclData = implode(",", $acl['data']);
		}
	    else
		{
			if(is_array($acl['data']))
				{
						
					$aclData  = $acl['data'][0];
				}
			else
				{
					$aclData = $acl['data'];
				}
			
		}
		
	if($acl['type'] == 'profile')
		{
		    $aclData .=  ",\"".$acl['comparison']."\"";
    		    $result = $acl['type']."(".$acl['profile']."_".$aclData.")";

		}
	else {
		  $aclData = "\"".$aclData."\"";
	   	  $aclData .=  ",\"".$acl['comparison']."\"";
		  $result = $acl['type']."(".$aclData.")";
		}

            if(!empty($aclData))
            {
                if (!empty($compiledAcls))
                {

                    $compiledAcls[] = $acl['logical'];

                }
                $compiledAcls[] = $result;
		$result = '';
		$aclData = '';
            }
        }
	 $sLimitation = implode(' ', $compiledAcls);

    return $sLimitation;
    }
}

function mobileAclSave($acls, $aEntities)
{
            $table      = 'banners';
            $aclsTable  = 'acls';
            $fieldId    = 'bannerid';
   
    $aclsObjectId = $aEntities[$fieldId];
    $sLimitation = getMobileLimitations($acls);

    $doTable = OA_Dal::factoryDO($table);

    $doTable->$fieldId = $aclsObjectId;

    $found = $doTable->find(true);

    if ($sLimitation == $doTable->compiledlimitation)
    {
        return true; // No changes to the ACL
    }

    $doAcls = OA_Dal::factoryDO($aclsTable);
    $doAcls->whereAdd($fieldId.' = '.$aclsObjectId);
    $doAcls->delete(true);

    if (!empty($acls))
    {

        foreach ($acls as $acl)
        {

           	    $acl_type = explode("_",$acl['type']);

            $doAcls = OA_Dal::factoryDO($aclsTable);
            $doAcls->$fieldId   = $aclsObjectId;


            $doAcls->logical    = $acl['logical'];
		if($acl_type[0] == 'profile')
			{
				        $doAcls->type       = $acl_type[0];
			}
		else {
	  				 $doAcls->type       = $acl['type'];
			}
            $doAcls->comparison = $acl['comparison'];
		if(count($acl['data']) > 1)
		{
		   if($acl_type[0] == 'profile')
			{

						 $doAcls->data       =  $acl['profile']."_".implode(",", $acl['data']);
				
			}
		   else {
	  				$doAcls->data       = implode(",", $acl['data']);
			}
            		
		}
		else
		{
			if(is_array($acl['data']))
				{
							   if($acl_type[0] == 'profile')
								{
											 $doAcls->data       =  $acl['profile']."_".$acl['data'][0];
									
								}
		 						  else {
	  								  $doAcls->data       = $acl['data'][0];
								}	

				}
			else
				{
							  if($acl_type[0] == 'profile')
								{
						
											 $doAcls->data       =  $acl['profile']."_".$acl['data'];

								
								}
		 						  else {
	  								$doAcls->data       = $acl['data'];
								}
				}
		}

            $doAcls->executionorder = $acl['executionorder'];

            $id = $doAcls->insert();


            if (!$id)
            {
                return false;
            }

        }

    }


    $doTable->acl_plugins = MAX_AclGetPlugins($acls);
    $doTable->compiledlimitation = $sLimitation;
    $doTable->acls_updated = OA::getNowUTC();
    $doTable->update();

	$query = mysql_query("select * from rv_banners where bannerid =".$aclsObjectId) or die(mysql_error());
	$row = mysql_fetch_array($query); 

	$querynew = mysql_query("select * from rv_banners where masterbanner =".$aclsObjectId." order by bannerid ") or die(mysql_error());

	while($rownew = mysql_fetch_array($querynew))
	{

		mysql_query("update rv_banners set compiledlimitation = '".$row['compiledlimitation']."', acl_plugins = '".$row['acl_plugins']."' where bannerid=".$rownew['bannerid']." ") or die(mysql_error());

	}

    return true;
}

// Display Purpose Functions//
		//================This is used to fetch details of row=========================//
	function fetchrow($data)
	{

	$rowvalues = array();

	$query = mysql_query("select * from oxm_".$data." ") or die(mysql_error());
	if($data == 'profile')
		{

			while($row = mysql_fetch_assoc($query))
				{
			$rowvalues[$row['country_code']] = $row['name'];
				} 
		} elseif($data == 'country') {
				while($row = mysql_fetch_assoc($query))
				{
					$rowvalues[$row['country_code']] = $row['name'];
				} 
		} else {

				while($row = mysql_fetch_assoc($query))
				{
					$rowvalues[$row['name']] = $row['name'];
				} 


		}
	return $rowvalues;
	}
	function getProfile($data)
	{

	$rowvalues = array();

	$query = mysql_query("select * from oxm_".$data." ") or die(mysql_error());
		while($row = mysql_fetch_assoc($query))
		{
			$rowvalues[$row['name']] = $row['name'];
		} 
	return $rowvalues;
	}

                      /***smart phone***/
                 /* function getSmart($data)
                   {
                  $rowvalues = array();
                 $query = mysql_query("select * from oxm_".$data." ") or die(mysql_error());

                           if($data == 'smart'){

                     while($row = mysql_fetch_assoc($query))
                                {

                       $rowvalues[$row['name']] = $row['name'];


                                }

                  return $rowvalues;
                              }
*/

                        /***smart phone***/


		//================This is used to fetch Name of Delivery=========================//
	 function getLimitName($data)
   	  {
			if($data == 'handset')
			{
				$name = "Handset - Manufacture";
				return $name;
			}
			elseif($data == 'os')
			{
				$name = "Handset - Operating System";
				return $name;
			}
			elseif($data == 'model')
			{
				$name = "Handset - Model Name";
				return $name;
			}
			elseif($data == 'teleco')
			{
				$name = "Handset - Teleco Operator";
				return $name;
			}
			elseif($data == 'country')
			{

				$name = "Client - Country";
				return $name;
			}/*SMARTPHONE*/
                        elseif($data == 'smart')
                             {

                                $name = "Mobile - Smart phone";
				return $name;
                             }/*SMARTPHONE*/
			else
			{

				$name = "Client - Custom - Profile ";

				return $name;
			}
  	  }

			//================This is used to fetch details of comparison based on delivery type=========================//
	function mobiledisplayComparison($aOperations)
   		 {
			if($aOperations == 'model')
			{
				$cmp = array("=="=> "is equal to", "!="=> "is different from", "=~"=> "Contains", "!~"=> "Does not contain");
				return $cmp;
			}
			elseif($aOperations == 'handset')
			{
				
				$cmp = array("=~"=> "Is any of", "!~"=> "Is not any of");
				return $cmp;
			}
			elseif($aOperations == 'os')
			{	
				$cmp = array("=~"=> "Is any of", "!~"=> "Is not any of");
				return $cmp;
			}
			elseif($aOperations == 'teleco')
			{	
				$cmp = array("=~"=> "Is any of", "!~"=> "Is not any of");
				return $cmp;
			}
			elseif($aOperations == 'country')
			{
				
				$cmp = array("=~"=> "Is any of", "!~"=> "Is not any of");
				return $cmp;
			}

                          elseif($aOperations == 'smart')/*SMARTPHONE*/
			{
				
				$cmp = array("=~"=> "Is any of", "!~"=> "Is not any of");
				return $cmp;
			}/*SMARTPHONE*/



			else
			{

			$cmp = array("=="=> "is equal to", "!="=> "is different from", "=~"=> "Contains", "!~"=> "Does not contain", "<="=> "Is less than or equal to", ">="=> "Is grater than or equal to");
				return $cmp;
			}
		
     
  		  }

		//================This is used for display all the values=========================//
    function mobiledisplay($row, $count)
    {

	$newArray = array("0" => "handset", "1" => "os", "2" => "teleco", "3" => "country","4"=> "smart" );

	if(in_array($row['type'], $newArray))
		{
		$newrow = fetchrow($row['type']); 
		}
	$name = getLimitName($row['type']);

	$aOperations = mobiledisplayComparison($row['type']);


        global $tabindex;
        if ($row['executionorder'] > 0) {
            echo "<tr><td colspan='4'><img src='" . OX::assetPath() . "/images/break-el.gif' width='100%' height='1'></td></tr>";
        }

        $bgcolor = $row['executionorder'] % 2 == 0 ? "#E6E6E6" : "#FFFFFF";

                         

			if($row['type'] == 'country')
			{

				$checkingCountry = $GLOBALS['_MAX']['CONF']['geotargeting']['type'];

				if(empty($checkingCountry)) {

					echo "<font color='#FF0000' size='3'>Enable GeoTargeting Plugin to get Country Based Targeting functionality.</font><br/><br/>";
					echo "<font color='#339999'>(Goto the Administrator account and click configuartion tab then select the 'Geotargeting settings'.<br/>

						Select the module type <strong>'Openx MaxMind FlatFile'</strong> properties.

						Check <strong>'Show geotargeting delivery limitations even if GeoIP data unavailable'</strong>  properties .</font>";
				}
			}

	//================This is used for display all the comparation and logical operations=========================//
        echo "<tr height='35' bgcolor='$bgcolor'>";
        echo "<td width='100'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if ($row['executionorder'] == 0) {
            echo "<input type='hidden' name='acl[{$row['executionorder']}][logical]' value='and'>&nbsp;";
        } else {
            echo "<select name='acl[{$row['executionorder']}][logical]' tabindex='".($tabindex++)."'>";
            echo "<option value='or' " . (($row['logical']== 'or') ? 'selected' : '') . ">{$GLOBALS['strOR']}</option>";
            echo "<option value='and' " . (($row['logical'] == 'and') ? 'selected' : '') . ">{$GLOBALS['strAND']}</option>";
            echo "</select>";
        }
        echo "</td><td width='130'>";
		echo "<table cellpadding='2'><tr><td><img src='" . OX::assetPath() . "/images/icon-acl.gif' align='absmiddle'>&nbsp;</td><td>{$name}</td></tr></table>";
		echo "<input type='hidden' name='acl[{$row['executionorder']}][type]' value='{$row['type']}'>";
		echo "<input type='hidden' name='acl[{$row['executionorder']}][executionorder]' value='{$row['executionorder']}'>";
		echo "</td><td >";
	if($row['type'] == 'profile')
		{
			$getProfile = getProfile($row['type']);
	if($row['data'] == '')
			{
			 	echo "<select name='acl[{$row['executionorder']}][profile]' tabindex='".($tabindex++)."'>";
			 	foreach($getProfile as $value => $sDescription) {
				    echo "<option value='$value' " . (($row['profile'] == $value) ? 'selected' : '') . ">$sDescription</option>";
				}
				echo "</select>";
				echo "&nbsp;&nbsp;&nbsp;";
			} else {

					$newData = explode("_", $row['data']);
					if(count($newData) > 1)
					{
					 	echo "<select name='acl[{$row['executionorder']}][profile]' tabindex='".($tabindex++)."'>";
					 	foreach($getProfile as $value => $sDescription) {
						    echo "<option value='$value' " . (($newData[0] == $value) ? 'selected' : '') . ">$sDescription</option>";
						}
						echo "</select>";
						echo "&nbsp;&nbsp;&nbsp;";
					} else {
							echo "<select name='acl[{$row['executionorder']}][profile]' tabindex='".($tabindex++)."'>";
						 	foreach($getProfile as $value => $sDescription) {
							    echo "<option value='$value' " . (($row['profile'] == $value) ? 'selected' : '') . ">$sDescription</option>";
							}
							echo "</select>";
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
			}
	}


 	echo "<select name='acl[{$row['executionorder']}][comparison]' tabindex='".($tabindex++)."'>";
 	foreach($aOperations as $sOperator => $sDescription) {
            echo "<option value='$sOperator' " . (($row['comparison'] == $sOperator) ? 'selected' : '') . ">$sDescription</option>";
        }
		echo "</select>";
        echo "</td>";

	//===============================================================================================================//
        // Show buttons
	//================This is used for assign execution order and delete particular limitations one by one=========================//			
		echo "<td align='{$GLOBALS['phpAds_TextAlignRight']}'>";
		echo "<input type='image' name='action[del][{$row['executionorder']}]' value='{$row['executionorder']}' src='" . OX::assetPath() . "/images/icon-recycle.gif' border='0' align='absmiddle' alt='{$GLOBALS['strDelete']}'>";
		echo "&nbsp;&nbsp;";
		echo "<img src='" . OX::assetPath() . "/images/break-el.gif' width='1' height='35'>";
		echo "&nbsp;&nbsp;";

		if ($row['executionorder'] && $row['executionorder'] < $count)
			echo "<input type='image' name='action[up][{$row['executionorder']}]' src='" . OX::assetPath() . "/images/triangle-u.gif' border='0' alt='{$GLOBALS['strUp']}' align='absmiddle'>";
		else
			echo "<img src='" . OX::assetPath() . "/images/triangle-u-d.gif' alt='{$GLOBALS['strUp']}' align='absmiddle'>";

		if ($row['executionorder'] < $count - 1) {
			echo "<input type='image' name='action[down][{$row['executionorder']}]' src='" . OX::assetPath() . "/images/triangle-d.gif' border='0' alt='{$GLOBALS['strDown']}' align='absmiddle'>";
		} else {
			echo "<img src='" . OX::assetPath() . "/images/triangle-d-d.gif' alt='{$GLOBALS['strDown']}' align='absmiddle'>";
		}
		
		echo "&nbsp;&nbsp;</td></tr>";
	//===============================================================================================================//		
		echo "<tr bgcolor='$bgcolor'><td>&nbsp;</td><td>&nbsp;</td><td colspan='2'>";
		

			if($row['type'] == 'model')
			{

 echo "<input type='text' size='40' name='acl[{$row['executionorder']}][data]' value=\"".htmlspecialchars(isset($row['data']) ? $row['data'] : "")."\" tabindex='".($tabindex++)."'>";
        echo "<br /><br /><br/></td>";
			}
			elseif($row['type'] == 'profile')
			{

	if($row['data'] == '')
			{
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input type='text' size='40' name='acl[{$row['executionorder']}][data]' value=\"".htmlspecialchars(isset($row['data']) ? $row['data'] : "")."\" tabindex='".($tabindex++)."'>";
			} else {

					$newDataValue = explode("_", $row['data']);
					if(count($newDataValue) > 1)
					{
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input type='text' size='40' name='acl[{$row['executionorder']}][data]' value=\"".htmlspecialchars(isset($newDataValue[1]) ? $newDataValue[1] : "")."\" tabindex='".($tabindex++)."'>";
					} else {
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input type='text' size='40' name='acl[{$row['executionorder']}][data]' value=\"".htmlspecialchars(isset($row['data']) ? $row['data'] : "")."\" tabindex='".($tabindex++)."'>";
					}
			}
	
        echo "<br /><br /><br/></td>";

			}
			elseif($row['type'] == 'country')
			{
			  if(is_array($row['data']))
			    {
				echo "<div class='box'>";
				foreach ($newrow as $code => $name) {
				    echo "<div class='boxrow'>";
				    echo "<input tabindex='".($tabindex++)."' ";
				    echo "type='checkbox' id='c_{$row['executionorder']}_{$code}' name='acl[{$row['executionorder']}][data][]' value='{$code}'".(in_array($code, $row['data']) ? ' CHECKED' : '').">{$name}</div>";
				}
				echo "</div>";
			   } else {
					echo "<div class='box'>";
					foreach ($newrow as $code => $name) {
					    echo "<div class='boxrow'>";
					    echo "<input tabindex='".($tabindex++)."' ";
					    echo "type='checkbox' id='c_{$row['executionorder']}_{$code}' name='acl[{$row['executionorder']}][data][]' value='{$code}'".(in_array($code, explode(",", $row['data'])) ? ' CHECKED' : '').">{$name}</div>";
					}
					echo "</div>";
					
			   }
			  
			}
			else
			{

$i = 0;			echo "<table cellpadding='3' cellspacing='3'>";
		foreach ($newrow as $key => $value) {
			if ($i % 4 == 0) echo "<tr>";
				if(is_array($row['data']))
					{
			echo "<td><input type='checkbox' name='acl[{$row['executionorder']}][data][]' value='{$key}' ".(in_array($key, $row['data']) ? ' checked="checked" ' : '')." tabindex='".($tabindex++)."'>".ucfirst($value)."</td>";
					}
				else
					{
			echo "<td><input type='checkbox' name='acl[{$row['executionorder']}][data][]' value='{$key}' ".(in_array($key, explode(",", $row['data'])) ? ' checked="checked" ' : '')." tabindex='".($tabindex++)."'>".ucfirst($value)."</td>";
					}
			if (($i + 1) % 4 == 0) echo "</tr>";
			$i++;
		}
		if (($i + 1) % 4 != 0) echo "</tr>";
		echo "</table>";
			
			}
			
        echo "<br /><br /></td></tr>";

    }


?>
