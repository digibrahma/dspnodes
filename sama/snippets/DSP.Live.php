<?php

require_once("../incs/db.php");


$Minutes = date('i');
$Date = $_REQUEST['Date'];
$Service = $_REQUEST['Service'];

$Service_DESC[6]['Name'] = 'Smaato';
$Service_DESC[7]['Name'] = 'Axonix';



if(isset($_REQUEST['Service'])) {

	$Service = "'".join("','", $_REQUEST['Service'])."'";

} else {
    $Service = '';
	$GRP_Service = '';   
}


//echo count($_REQUEST['Service']);exit;


function Format($Num,$Decimal='0') {
global $Layout;
return ($Num==0)?"-":bd_nice_number($Num);
}




$Get_Live_Data = mysqli_query($dbLink,"select dsp as service, (Type) as 'type', value,  date, if(date_format(date,'%k')=0,1,date_format(date,'%k')+1) as hr from sama_livemis where dsp in (".$Service.") and date >='".$Date." 00:00:00' and date <= '".$Date." 23:00:00'  ") or die(mysqli_error());


$MasterRevenueArray = array('activation','renewal','top-up','event');
$Dip_Types = array();

while($Push_Data = mysqli_fetch_array($Get_Live_Data)) {
	
	$LiveData[ReCastType($Push_Data['type'])][$Push_Data['hr']] += $Push_Data['value'];
	$RevLiveData[ReCastType($Push_Data['type'])][$Push_Data['hr']] += $Push_Data['revenue'];


if(in_array(strtolower(ReCastType($Push_Data['type'])),$MasterRevenueArray)) {
	$RevLiveData['TotalRevenue'][$Push_Data['hr']] += $Push_Data['revenue'];
}

if(!in_array($Push_Data['type'],$Dip_Types)) {
	array_push($Dip_Types,$Push_Data['type']);
}

	
}
//print_r($LiveData);


/// T-1 Data for comparison ////
$Get_T_Minus_1_Data = mysqli_query($dbLink,"select dsp as service,  type, value, date, if(date_format(date,'%k')=0,1,date_format(date,'%k')+1) as hr from sama_livemis where dsp in (".$Service.") and date >='".date("Y-m-d",strtotime($Date)-24*60*60)." 00:00:00' and date <= '".date("Y-m-d",strtotime($Date)-24*60*60)." 23:00:00'  order by type ASC") or die(mysqli_error());

while($Push_T_Minus_1_Data = mysqli_fetch_array($Get_T_Minus_1_Data)) {
	
	$T_Minus_1_LiveData[ReCastType($Push_T_Minus_1_Data['type'])][$Push_T_Minus_1_Data['hr']] += $Push_T_Minus_1_Data['value'];
	$T_Minus_1_RevLiveData[ReCastType($Push_T_Minus_1_Data['type'])][$Push_T_Minus_1_Data['hr']] += $Push_T_Minus_1_Data['revenue'];


	if(in_array(strtolower(ReCastType($Push_T_Minus_1_Data['type'])),$MasterRevenueArray)) {
		$T_Minus_1_RevLiveData['TotalRevenue'][$Push_T_Minus_1_Data['hr']] += $Push_T_Minus_1_Data['revenue'];
	}


	
}



///  End T-1 Data for comparison ////



///// Create Arrays /////

$Mode_Activations = preg_filter('/^Mode_Activation_(.*)/', '$1', $Dip_Types);
$Mode_Deactivations = preg_filter('/^Mode_Deactivation_(.*)/', '$1', $Dip_Types);
$Mode_Clicks = preg_filter('/^Clicks_(.*)/', '$1', $Dip_Types);
$Mode_NOMSISDN = preg_filter('/^NOMSISDN_(.*)/', '$1', $Dip_Types);


///// End Arrays /////

//print_r($Mode_Activations);


function ReCastType($inType) {

	list($type,$waste) = explode("_",$inType);
	
	/*if(strcmp('mode',strtolower($type))==0) {
		
		list($type1,$type2,$junk) = explode("_",$inType);
		$type = $type1.'_'.$type2;
	}
	*/
	
	switch(strtolower($type)) {
	
	case 'activation':
		return 'Activation';
		break;
	case 'renewal':
		return 'Renewal';
		break;
	case 'event':
		return 'Event';
		break;
	case 'top-up':
		return 'Top-Up';
		break;
	case 'deactivation':
		return 'Deactivation';
		break;
	//case 'mode_deactivation':
	//	return $junk;
	//	break;
	
	default:
		return $inType;
	}

}

function MergeRow($Text) {
	return  '<tr><td colspan=26 class="alert alert-inverse" align="center" style="font-size:12px">'.$Text.'</td></tr>';
}

function bd_nice_number($n) {
    // first strip any formatting;
    $n = (0+str_replace(",","",$n));

    // is this a number?
    if(!is_numeric($n)) return false;

    // now filter it;
    if($n>1000000000000) return round(($n/1000000000000),1).' T';
    else if($n>1000000000) return round(($n/1000000000),1).' B';
    else if($n>1000000) return round(($n/1000000),1).' M';
    else if($n>1000) return round(($n/1000),1).' K';

    return number_format($n);
}


function CompareColors($Main,$Sec) {

	if($Main<$Sec) 
			return '#FFA3A3';
		elseif($Main==$Sec)
			return '#e2e2e2';
		else
			return '#A3DAB6';

}


function printLiveCells($tag,$type='',$secondtag='',$displayName='',$pad='',$rev = '') {
global $LiveData,$RevLiveData, $T_Minus_1_LiveData, $T_Minus_1_RevLiveData;

	if($rev=='2') {
		$UseWhichArray = 'RevLiveData';
		$UseWhichArray_T_Minus_1 = 'T_Minus_1_RevLiveData';
	} else{
		$UseWhichArray = 'LiveData';
		$UseWhichArray_T_Minus_1 = 'T_Minus_1_LiveData';
	}


	$cellData = '<tr><td>'.($displayName?$displayName:$tag).'</td>';
	
	switch($type) {
		
	case 'div':
		
		for($i=1;$i<=24;$i++) {
				$cellData .= '<td><nobr>'.number_format(${$UseWhichArray}[$tag][$i]*(($pad=='%')?100:1)/(${$UseWhichArray}[$secondtag][$i]==0?1:${$UseWhichArray}[$secondtag][$i]),0).$pad.'</nobr></td>';
		}
		
		$cellData .= '<td><nobr>'.number_format(((is_array(${$UseWhichArray}[$tag]))?array_sum(${$UseWhichArray}[$tag]):0)*(($pad=='%')?100:1)/((is_array(${$UseWhichArray}[$secondtag]))?array_sum(${$UseWhichArray}[$secondtag]):1),0).$pad.'</nobr></td>';
	break;
	case 'divk':
		
		for($i=1;$i<=24;$i++) {
				$cellData .= '<td><nobr>'.number_format(${$UseWhichArray}[$tag][$i]*(($pad=='%')?100:1)/(${$UseWhichArray}[$secondtag][$i]==0?1:${$UseWhichArray}[$secondtag][$i]),0).$pad.'</nobr></td>';
		}
		
		$cellData .= '<td><nobr>'.number_format(((is_array(${$UseWhichArray}[$tag]))?array_sum(${$UseWhichArray}[$tag]):0)*(($pad=='%')?100:1)/((is_array(${$UseWhichArray}[$secondtag]))?array_sum(${$UseWhichArray}[$secondtag]):1),0).$pad.'</nobr></td>';
	
		
	
	
	break;
	
	case 'gross':
		
		for($i=1;$i<=24;$i++) {
			$tmpSum = 0;
			$tmpSum_T_Minus_1 = 0;
			 	for($j=1;$j<=$i;$j++) {
			 			$tmpSum += ${$UseWhichArray}['TotalRevenue'][$j];
			 			$tmpSum_T_Minus_1 += ${$UseWhichArray_T_Minus_1}['TotalRevenue'][$j];
			 	}
			$cellData .= '<td bgcolor="'.CompareColors($tmpSum,$tmpSum_T_Minus_1).'"><nobr><strong>'.Format($tmpSum).'</strong></nobr></td>';
		 	
		}
			$cellData .= '<td bgcolor="'.CompareColors($tmpSum,$tmpSum_T_Minus_1).'"><nobr><strong>'.Format($tmpSum).'</strong></nobr></td>';
		
	break;
	
	default:	
		for($i=1;$i<=24;$i++) {
				$cellData .= '<td bgcolor="'.CompareColors(${$UseWhichArray}[$tag][$i],${$UseWhichArray_T_Minus_1}[$tag][$i]).'"><nobr>'.Format(${$UseWhichArray}[$tag][$i]).'</nobr></td>';
		}
		
		$cellData .= '<td bgcolor="'.CompareColors(((is_array(${$UseWhichArray}[$tag]))?array_sum(${$UseWhichArray}[$tag]):0),((is_array(${$UseWhichArray_T_Minus_1}[$tag]))?array_sum(${$UseWhichArray_T_Minus_1}[$tag]):0)).'"><nobr>'.Format((is_array(${$UseWhichArray}[$tag]))?array_sum(${$UseWhichArray}[$tag]):0).'</nobr></td>';
	
		}
	
	
	$cellData .= '</tr>';
	return $cellData;
}


?>
		
        
	<div class="alert alert-success"><a href="#" id="Refresh"><i class="fui-eye"></i></a> Displaying Live DSP KPI's for <strong><?php echo (count($_REQUEST['Service'])==1)? $Service_DESC[($_REQUEST['Service'][0])]["Name"]:count($_REQUEST['Service']). ' DSPs ';?></strong> for <?php echo date("F j",strtotime($Date));?></div>  
      <script>
      
      $('#Refresh').on('click', function() {
		  $('#go').trigger('click');
	  });
      </script> 
      
      
     <div class="table-responsive"> 
     
  <table class="table table-bordered table-condensed" style="font-size: 10px;">
      <tbody> <tr class="info">
        <th>TIME</th>
        <th width="4%" align="center">1</th>
        <th width="4%" align="center">2</th>
        <th width="4%" align="center">3</th>
        <th width="4%" align="center">4</th>
        <th width="4%" align="center">5</th>
        <th width="4%" align="center">6</th>
        <th width="4%" align="center">7</th>
        <th width="4%" align="center">8</th>
        <th width="4%" align="center">9</th>
        <th width="4%" align="center">10</th>
        <th width="4%" align="center">11</th>
        <th width="4%" align="center">12</th>
        <th width="4%" align="center">13</th>
        <th width="4%" align="center">14</th>
        <th width="4%" align="center">15</th>
        <th width="4%" align="center">16</th>
        <th width="4%" align="center">17</th>
        <th width="4%" align="center">18</th>
        <th width="4%" align="center">19</th>
        <th width="4%" align="center">20</th>
        <th width="4%" align="center">21</th>
        <th width="4%" align="center">22</th>
        <th width="4%" align="center">23</th>
        <th width="4%" align="center">24</th>
        <th width="4%" align="center">Total</th></tr>
        <?php
		
		
		
		
					include 'livemap.default.php';
		
		
		?>
    <tr>
         </tr>
  	</tbody>
  
</table> 
    
    </div>
    <div class="col-md-3">
    		<table class="table table-bordered table-condensed" style="font-size: 11px;" width="20%">
      		<tbody> 
      		<tr class="info">
       		 <th>Legend</th>
       		 </tr>
       		 
       		 <tr>
       		 		<td bgcolor="<?php echo CompareColors(1,2);?>">Lower than yesterday's same timeslot</td>
       		 </tr>
       		 
       		 <tr>
       		 		<td bgcolor="<?php echo CompareColors(2,1);?>">Higher than yesterday's same timeslot</td>
       		 </tr>
       		 
       		 <tr>		
       		 		<td bgcolor="<?php echo CompareColors(1,1);?>">Same as yesterday's same timeslot</td>
       		 </tr>
       		 
       		 </tbody>
       		 </table>
       		 
    </div>  