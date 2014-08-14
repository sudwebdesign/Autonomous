$css('select2');
$js(true,[
	'jquery',
	'select2',
	'select2/fr'
],function(){
	$('[is=select2]').each(function(){
		var THIS = $(this);
		THIS.select2({
			dropdownAutoWidth:true,
			width:'100%'
		});
	});
});