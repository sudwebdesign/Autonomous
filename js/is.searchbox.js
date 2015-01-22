$css('jquery-ui/core');
$css('jquery-ui/menu');
$css('jquery-ui/autocomplete');

$js('jquery',function(){
	$('[is=searchbox]').each(function(){
		var input = $(this).find('input[type=search]');
		$(this).submit(function(e){
			e.preventDefault();
			var val = input.val();
			if(val){
				val = '+search:'+val;
			}
			window.location = window.location.protocol+'//'+window.location.hostname+'/Projects'+val;
			return false;
		});
		$js([
			'jquery-ui/core',
			'jquery-ui/widget',
			'jquery-ui/menu',
			'jquery-ui/position',
			'jquery-ui/autocomplete'
		],true,function(){
			input.autocomplete({
				source:input.attr('data-url'),
				selectFirst:true,
				autoFill:true,
				minLength: 3
			});
		});
	});
});