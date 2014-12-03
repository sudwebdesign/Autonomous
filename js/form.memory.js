$js(true,[
	'jquery',
	'is.ckeditor',
	'is.daterange',
],function(){
	<!--#include virtual="/js/sisyphus.js" -->
	$('main>form[id][action][role=form]:not(.form-posted)').sisyphus();
});