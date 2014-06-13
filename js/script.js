$js('jquery',function(){
	var location = document.location.pathname;
	location = decodeURIComponent(location.substr(1));
	$('body>nav>ul[is=dropdown] li>a[href="'+location+'"]').each(function(){
		var li = $(this).parent('li');
		if(!$(this).closest('ul').is('[is=dropdown]')){
			li = $(this).closest('li');
		}
		li.addClass('active');
		li.parent('ul').parent('li').addClass('active');
	});
	$(window).on('unload',function(){
		$('main').css('opacity',0.5);
	});
});
<!--#include virtual="/js/retro.js" -->
$css('print.min','print');
