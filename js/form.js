<!--#include virtual="./form.memory.js" -->
<!--#include virtual="./form.validation.js" -->
$js('jquery',function(){
	//$('input[name][required]').prev('legend').addClass('required');
	$('input[name][required]').after('<div class="required" style="float:left;" />');
});