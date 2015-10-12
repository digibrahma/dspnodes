function openPortal(id)
{
	
	if($('#' + id).is(":checked"))
	{
		$('.' + id).show();
	}
	else
	{
		$('.' + id).hide();
	}
	$(".list select").hide();
	
}
function getClick()
{
	var getCat	=[];
	$('input[class="dsp_portal"]:checked').each(function() {
   
	
		$('.'+this.id+' input:checked').each(function() {
			getCat.push(this.value)
		
	});
	$('input[name="'+this.value+'"]').val(getCat.toString());
	
	getCat	=[];
});

return true;
	
}
$(document).ready(function() { 
		$('input[class="dsp_portal"]').each(function()
		{
			$('.' + this.id).multipleSelect();	
			$(".list select").hide();
		});
		$('input[class="dsp_portal"]:checked').each(function()
		{
			$('.' + this.id).show();
			$(".list select").hide();
		});
	});
