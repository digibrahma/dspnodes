<?php

define('CONST_CSS','

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">

<!-- Theme -->
<link rel="stylesheet" href="css/flat-ui.css">
<link rel="stylesheet" href="css/hungamaweb.css">
<link rel="stylesheet" href="css/bootstrap-select.css">
<link rel="stylesheet" href="css/bootstrapValidator.min.css"/>

<!-- IE Issues Fix -->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.min.js"></script>
      <script src="js/respond.js"></script>
    <![endif]-->

<!-- Fonts & Icons -->
<link rel="stylesheet" href="css/fontello.css">
<!--[if IE 7]>
    <link rel="stylesheet" href="css/fontello-ie7.css">
<![endif]-->


<script>
function toggle(id,rf_id){
		
		
		//alert(id);
		if( $("#all-"+rf_id).is(":visible") ) {
			$("#"+id).selectpicker("selectAll");
			$("#all-"+rf_id).toggle();
			$("#none-"+rf_id).toggle();
		}
		else {
			$("#"+id).selectpicker("deselectAll");
			$("#all-"+rf_id).toggle();
			$("#none-"+rf_id).toggle();
		
		}
	}	
</script>

');


//Footers
define('CONST_JS','<!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
<!-- Latest compiled and minified JavaScript -->
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="js/jquery.ui.touch-punch.min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/bootstrap-switch.js"></script>
    <script src="js/flatui-checkbox.js"></script>
    <script src="js/flatui-radio.js"></script>
    <script src="js/jquery.placeholder.js"></script>
    <script src="js/jquery.tagsinput.js"></script>
	<script type="text/javascript" src="js/bootstrapValidator.min.js"</script>
	
	<script>
	'.((STANDALONE_SELECT==true)?'':'$("select").selectpicker({style: \'btn btn-primary\', menuStyle: \'dropdown-inverse\'});').'
	
	
	</script>');

// Date Range JS
define('DATERANGE_JS','
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.js"></script>');

define('DATERANGE_CSS','
<link rel="stylesheet" type="text/css" media="all" href="css/daterangepicker.css" />');

define('DATEPICKER_JS','<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>');

// Moment.js

define('MOMENT_JS','<script type="text/javascript" src="js/moment.min.js"></script>');

// Typehead
define('TYPEAHEAD_JS','
<script type="text/javascript" src="js/typeahead.js"></script>');
define('TYPEAHEAD3_JS','
<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>');

// Autocomplete
define('AUTOCOMPLETE_JS','
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>');

// Boostrap Wizard JS
define('WIZARD_JS','
<script type="text/javascript" src="js/bootstrap.wizard.js"></script>');


// Chart JS

define('CHARTS_JS','<script src="charts/jquery.sparkline.min.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="charts/excanvas.min.js"></script><![endif]-->
<script src="charts/jquery.flot.min.js"></script>
<script src="charts/jquery.flot.pie.min.js"></script>
<script src="charts/jquery.flot.stack.min.js"></script>
<script src="charts/jquery.flot.resize.min.js"></script>
<script src="charts/jquery.flot.time.min.js"></script>
<script src="charts/jquery.flot.tooltip.min.js"></script>');


// Chart Colors & Patters
define('CHART_COLOR_TRENDLINE_1','#bdea74');
define('CHART_COLOR_TRENDLINE_2','#eae874');
define('CHART_COLOR_TRENDLINE_3','#2FABE9');
define('CHART_COLOR_TRENDLINE_4','#FA5833');
define('CHART_COLOR_TRENDLINE_5','#ff0000');
define('CHART_COLOR_TRENDLINE_6','#000000');
define('CHART_COLOR_TRENDLINE_TOOLTIPBG','#dfeffc');
define('CHART_COLOR_TRENDLINE_TOOLTIPBORDER','#dfeffc');
define('CHART_COLOR_GRID_TICK','#f9f9f9');
define('CHART_DATE_FORMAT','%d-%b');


?>