$js('jquery',function(){
	<!--#include virtual="./validate.js" -->
	var onSubmit = function(e){
		e.preventDefault();
		var email = $(document).data('persona.email');
		var THIS = $(this);
		var submit = function(){
			THIS.off('submit',onSubmit).submit();
		};
		if(email){
			submit();
		}
		else{
			$(document).one('persona.login',submit);
			$('[is=persona]:first').click();
		}
		return false;
	};
	var form = $('main form[action][id]');
	form.on('submit',onSubmit);
	$('input[type=url]',form).on('keyup',function(){
		var self = $(this);
		var val = self.val();
		var oval = val;
		try{
			val = decodeURIComponent(val);
		}
		catch(e){}
		var prefixs,prefix,prefixing,brk;
		prefixs = ['https://','http://']
		if(val.indexOf('://')<0){
			if(val){
				for(var y=0;y<prefixs.length;y++){
					prefix = prefixs[y];
					prefixing = '';
					for(var i=0;i<prefix.length;i++){
						prefixing += prefix.charAt(i);
						if(val==prefixing){
							val = '';
							brk = true;
							break;
						}
					}
					if(brk){
						break;
					}
				}
				val = prefix+val;
			}
		}
		if(oval!=val){
			self.val(val);
		}
	});
});