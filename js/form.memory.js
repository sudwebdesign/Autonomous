$js(true,[
	'jquery',
	'/x-dom/ckeditor.js',
	'/x-dom/daterange.js',
],function(){
	<!--#include virtual="/js/sisyphus.js" -->
	$('main>form[id][action][role=form]:not(.form-posted)').sisyphus({
		excludeFields:[$('input[name="xownGeopoint[label]"]').get(0),$('input[name="use_address"]').get(0)]
	});
});