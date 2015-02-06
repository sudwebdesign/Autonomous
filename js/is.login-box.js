$css('is.login-box');
$css('jquery-ui/core');
$css('jquery-ui/button');
$css('jquery-ui/dialog');
$js([
	'jquery',
	'jquery-ui/core',
	'jquery-ui/widget',
	'jquery-ui/button',
	'jquery-ui/dialog'
],true,function(){
	var loaded,login,dialog,loadDialog,hanldeForm;
	login = $('<div class="login-dialog" title="Login / Sign-in"></div>').appendTo('body');
	loadDialog = function(){
		$.get('login-box',function(html){
			dialog = login.dialog({
				modal:true,
				width:'85%'
			});
			dialog.html(html);
			hanldeForm();
			loaded = true;
		});
	};
	hanldeForm = function(){
		dialog.find('form').submit(function(e){
			e.preventDefault();
			var form,url;
			form = $(this);
			url = 'login-box';
			url += '?'+form.attr('action').split('?')[1];
			$.post(url,form.serialize(),function(html){
				dialog.html(html);
				hanldeForm();
			});
			return false;
		});
	};
	
	$.getJSON('service/auth/email',function(email){
		if(email){
			logonCALL(email);
			logoutBTN.on('click',function(){
				out = true;
				initPersona();
			});
		}
		else{
			init = true;
			loginBTN.on('click',function(){
				initPersona(true);
			}).show();
		}
		$('[is="login-box"]').show().click(function(e){
			e.preventDefault();
			if(loaded){
				dialog.dialog('open');
			}
			else{
				loadDialog();
			}
			return false;
		});
	});	
});