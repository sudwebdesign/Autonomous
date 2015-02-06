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
	
	
	
	var loginBTN = $('a[is=login-box]'),
		logoutBTN = $('a.logout');
	var loginCALL = function(e){
		return navigator.id.request({
			siteName: loginBTN.attr('data-sitename'), //Plain text name of your site to show in the login dialog. Unicode and whitespace are allowed, but markup is not.
			backgroundColor:loginBTN.attr('data-bgcolor'),
			//oncancel:function(){}, //invoked if the user refuses to share an identity with the site.
			//privacyPolicy:'/Politique-Confidentialité', //Must be served over SSL. The termsOfService parameter must also be provided. Absolute path or URL to the web site's privacy policy. If provided, then termsOfService must also be provided. When both termsOfService and privacyPolicy are given, the login dialog informs the user that, by continuing, "you confirm that you accept this site's Terms of Use and Privacy Policy." The dialog provides links to the the respective policies.
			//returnTo: window.location, //Absolute path to send new users to after they've completed email verification for the first time. The path must begin with '/'. This parameter only affects users who are certified by Mozilla's fallback Identity Provider. This value passed in should be a valid path which could be used to set window.location too.
			//siteLogo: '/img/logo.png', //Must be served over SSL. Absolute path to an image to show in the login dialog. The path must begin with '/'. Larger images will be scaled down to fit within 100x100 pixels.
			//termsOfService: 'Termes-Utilisation', Optional Must be served over SSL. The privacyPolicy parameter must also be provided. Absolute path or URL to the web site's terms of service. If provided, then privacyPolicy must also be provided. When both termsOfService and privacyPolicy are given, the login dialog informs the user that, by continuing, "you confirm that you accept this site's Terms of Use and Privacy Policy." The dialog provides links to the the respective policies. 
		});
	};
	var logonCALL = function(infos){
		$(document).data('auth.email',infos.email);
		$(document).trigger('auth.login');
		loginBTN.data('origin',loginBTN.html());
		loginBTN.addClass('persona-connected');
		loginBTN.html('<span>'+infos.name+'</span>');
		loginBTN.show();
		loginBTN.off('click',loginCALL);
		loginBTN.next('ul').removeClass('disabled');
		if(infos.email){
			$js('md5',function(){
				var s = 24;
				loginBTN.prepend('<img src="http://www.gravatar.com/avatar/'+md5(infos.email)+'?s='+s+'" width="'+s+'" height="'+s+'" />');
			});
		}
	};
	var logoffCALL = function(){
		$(document).data('auth.email',false);
		$(document).trigger('auth.logout');
		loginBTN.html('<span>'+loginBTN.data('origin')+'</span>');
		loginBTN.next('ul').addClass('disabled');
		loginBTN.removeClass('persona-connected');
		loginBTN.on('click',loginCALL);
	};
	var out;
	var init;
	var initPersona = function(launch){
		$js('https://login.persona.org/include.js',function(){
			navigator.id.watch({
				onlogin: function(as){
					if(!out){
						$.post('service/auth/persona',{assertion:as},function(login){
							if(login.status==='okay')
								logonCALL(login.email);
						});
					}
				},
				onlogout: function(){
					if(out||!init){
						$.get('service/auth/logout',function(res){
							if(res=='ok'){
								logoffCALL();
								out = false;
							}
						});
					}
				},
				onready: function(){
					loginBTN.on('click',loginCALL);
					loginBTN.show();
					logoutBTN.on('click',function(){
						navigator.id.logout()
					});
					if(out)
						navigator.id.logout();
					if(launch)
						loginBTN.click();
					init = false;
				}
			});
		});
	};
	
	
	$.getJSON('service/auth/infos',function(infos){
		if(infos){
			logonCALL(infos);
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
		//$('[is="login-box"]').show().click(function(e){
			//e.preventDefault();
			//if(loaded){
				//dialog.dialog('open');
			//}
			//else{
				//loadDialog();
			//}
			//return false;
		//});
	});	
});