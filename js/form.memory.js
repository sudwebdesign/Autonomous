$js(true,[
	'jquery',
	'is.ckeditor',
	'is.daterange',
],function(){
	<!--#include virtual="./sisyphus.js" -->
	$('main>form[id][action][role=form]:not(.form-posted)').sisyphus();
});