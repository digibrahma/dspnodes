<?php
include "incs/constants.php";

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.png">
<title>LIVE DSP KPI's</title>
<?php
echo CONST_CSS;
?>
</head>

<body>
<div class="container">
<?php
//include "includes/menu.php";
?>
<div class="row">
	<form id="livemis">
<div class="col-md-4">
<select multiple="multiple" class="select-block" name="Service[]" id="Service" data-title="Select DSP">
				
				<option value="6">Smaato</option>
				<option value="7">Axonix</option>
                          
		  
	</select>&nbsp;<a href="javascript:toggle('Service','Service')" id="all-Service">[All]</a><a href="javascript:toggle('Service','Service')" id="none-Service" style="display:none">[X]</a>
</div>
 	 <div class="col-md-4">
     <select class="select-block" id="Date" name="Date">
	   <option value="<?php echo date("Y-m-d", time()-24*60*60);?>" ><?php echo date("F j", time()-24*60*60);?></option>
                <option value="<?php echo date("Y-m-d");?>"  selected><?php echo date("F j");?></option>
	</select></div>
      <div class="col-md-4">
      <a href="javascript:;" class="btn btn-block btn-primary" id="go">Go!</a>&nbsp;&nbsp;&nbsp;
      </div>
</form>
</div>    

<div class="row">
 	 <div class="col-md-12">
     <div id="loading"><img src="img/220.gif" border="0" /></div> 
     <div id="alert-success" class="alert alert-success"></div> 
     <div id="alert-error" class="alert alert-danger"></div> 
     </div>
         
</div>   
<div class="row">
 	 <div class="col-md-12">
	 <div id="grid"></div> 
     </div>
</div>  

<?php
echo CONST_JS;
?>

<script>
	$("select").selectpicker({style: 'btn btn-primary', menuStyle: 'dropdown-inverse'});
	
		$('#go').on('click', function() {
		//alert('Hello');
			$('#loading').show();
			$('#alert-success').hide();
			
			if($('#Service').val() == "1") {
				$('#loading').hide();
				$('#alert-error').html('Please select a service');
				$('#alert-error').show();
					
			} else if($('#Circle').val() == "1") {
				$('#loading').hide();
				$('#alert-error').html('Please select a circle');
				$('#alert-error').show();
				
			} else{
			
			$('#grid').hide();
			$('#grid').html('');
			$('#alert-error').hide();

			$.ajax({
				url: 'snippets/DSP.Live.php',
				data: $('#livemis').serialize(),
				type: 'post',
				cache: false,
				dataType: 'html',
				success: function (xhr) {

						$('#loading').hide();
						$('#grid').html(xhr);
						$('#grid').show();
	
	
				}
				
			});
			}
	});



$('#loading').hide();
$('#alert-success').hide();
$('#alert-error').hide();
$('#grid').hide();
</script>
<style>
select {
display: inline;	
}
</style>
</body>
</html>