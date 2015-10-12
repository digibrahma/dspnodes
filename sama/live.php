<?php
include "incs/db.php";
include "incs/queries.php";

$Query = mysqli_query($query[1]) or die(mysqli_error());

while($result=mysqli_fetch_array($Query)) {

		$DATA[ReCastType($result['type'])][$result['hr']] += $result['value'];

}





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


?>