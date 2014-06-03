$js(true,[
	'jquery',
	'jquery-ui/core',
	'jquery-ui/widget'
],function(){
	var loc = document.location.pathname;
	var i = loc.indexOf(':');
	var i2 = loc.indexOf('|');
	if(i!==-1)
		loc = loc.substr(0,i);
	else if(i!==-1)
		loc = loc.substr(0,i2);
	loc = decodeURIComponent(loc.substr(1));
	//var li = $('body>nav>[is=dropdown]>li>a[href="'+loc+'"]').parent('li');
	//li.addClass('active');
	//li.parent('ul,[is=dropdown]').parent('li').addClass('active');
	//var mls = 'body>nav>[is=dropdown]>li>a[rel!="external"][target!="_blank"]:not([href^="javascript:"]):not([is=collapser])';
	//$(mls).on('click',function(){
		//$(mls).removeClass('active');
		//$(this).addClass('active');
		//$('main').css('opacity',0.5);
	//});
	$(window).on('unload',function(){
		$('main').css('opacity',0.5);
	});
});

var ie = (function(){var undef,v = 3,div = document.createElement('div'),all = div.getElementsByTagName('i');while(div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',all[0]);return v>4?v:undef;}());
if(ie&&ie<9)
	$js(['html5shiv','local-storage.retro']);

//if(typeof window.matchMedia=="undefined"&&typeof window.msMatchMedia=="undefined")
	//$js('respond');
	
$js([
	'jquery',
	'local-storage',
	'https://login.persona.org/include.js',
	'persona',
],function(){
	$.getJSON('service/persona/email',function(email){
		Persona(email);
	});
});

$css('print.min','print');
