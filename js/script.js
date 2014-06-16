$js('jquery',function(){
	var splitter = function(loc,splitters){
		for(var i in splitters){
			var idf = loc.indexOf(splitters[i]);
			if(idf>-1){
				loc = loc.substr(0,idf);
			}
		}
		return loc;
	};
	var location = document.location.pathname;
	location = decodeURIComponent(location.substr(1));
	var splitters = ['|','/',':'];
	var loc = splitter(location,splitters);
	var splitOnce = splitters.shift();
	var loc2 = splitter(location,splitters);
	var idf = loc2.indexOf(splitOnce);
	if(idf>-1){
		var x = loc2.split(splitOnce);
		loc2 = x[0]+splitOnce+x[1];
	}
	console.log(loc2);
	$('body>nav>ul[is=dropdown]>li:has(>a[href^="'+loc+'"])'
		+',body>nav>ul[is=dropdown]>li>ul>li:has(>a[href^="'+loc2+'"])'
		+',body>footer>a[href="'+location+'"]').addClass('active');
	
	$(window).on('unload',function(){
		$('main').css('opacity',0.5);
	});
});
<!--#include virtual="/js/retro.js" -->
$css('print.min','print');
