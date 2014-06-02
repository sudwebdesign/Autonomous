$js('jquery',function(){
	<!--#include virtual="/js/validate.js" -->
	$(function(){
		
		$('main form[action][id]').on('submit',function(e){
			e.preventDefault();
			$('body>header a.login').click();
			//$(this).off('submit').submit();
			return false;
		});
	});
});
