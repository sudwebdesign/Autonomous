$css('is.login-box');
$css('jquery-ui/core');
$css('jquery-ui/button');
$css('jquery-ui/dialog');
$js([
	'jquery',
	'jquery-ui/core',
	'jquery-ui/widget',
	'jquery-ui/button',
	'jquery-ui/dialog',
	'reload',
	'launch'
],true,function(){
	var
		loaded,
		login,
		dialog,
		loadDialog,
		hanldeDialog,
		handleMenu,
		loggedOn,
		loggedOff,
		loginInit,
		onPersona,
		initPersona,
		Persona,
		loadPersona,
		dialogInitHanldle
	;

	onPersona = false;
	loginBox = $('<div class="login-dialog" title="Login / Register"></div>').appendTo('body');
	loadDialog = function(){
		$.get('login-box',function(html){
			loginInit = html;
			dialog = loginBox.dialog({
				modal:true,
				width:'85%',
				close:function(){
					dialog.html(loginInit);
					dialogInitHanldle();
				}
			});
			dialog.on('login',loggedOn);
			dialog.on('logout',loggedOff);
			hanldeDialog(html);
			loaded = true;
		});
	};
	handleMenu = function(){
		//$('a.logout').click(function(e){
			//e.preventDefault();
			//loggedOff();
			//return false;
		//});
	};
	hanldeDialog = function(html){
		dialog.html(html);
		dialog.launch();
		dialog.find('form').submit(function(e){
			e.preventDefault();
			var form,url;
			form = $(this);
			url = 'login-box';
			url += '?'+form.attr('action').split('?')[1];
			$.post(url,form.serialize(),function(html){
				hanldeDialog(html);
			});
			return false;
		});
		dialog.find('a[href][href^="#"][href^="javascript:"]').click(function(e){
			e.preventDefault();
			$.get($(this).attr('href'),function(html){
				dialog.html(html);
				hanldeDialog(html);
			});
			return false;
		});
		loadPersona();
	};
	loggedChange = function(){
		dialog.reload('body');
		handleMenu();
		setTimeout(function(){
			dialog.dialog('close');
		},500);
	};
	loggedOn = function(){
		loggedChange();
	};
	loggedOff = function(e){
		$.get('service/auth/logout',function(res){
			if(res=='ok'){
				if(e){
					loggedChange();
				}
				else{
					console.log('user.body');
					$.reloader('user','body');
				}
				if(onPersona){
					Persona(function(){
						navigator.id.logout();
					});
				}
			}
		});
	};
	Persona = function(readyCallback){
		if(!initPersona){
			$js('https://login.persona.org/include.js',function(){
				navigator.id.watch({
					loggedInUser: null,
					onlogin: function(as){
						$.post('service/auth/persona',{assertion:as},function(login){
							if(login.status==='okay'){
								var form = $('form.login');
								$('fieldset.login',form).hide();
								$('fieldset.email',form).show();
								$('.links',form).hide();
								$('input[name="email"]',form).val(login.email);
								onPersona = true;
							}
						});
					},
					onlogout: function(){
						onPersona = false;
						$('fieldset.email').hide();
						$('fieldset.login').show();
						$('.links').show();
					},
					onready: function(){
						if(readyCallback)
							readyCallback();
						initPersona = true;
					}
				});
			});
		}
		else{
			if(readyCallback)
				readyCallback();
		}
	};
	loadPersona = function(){
		var loggerPersona = $('a.login.persona');
		loggerPersona.show();
		loggerPersona.click(function(e){
			e.preventDefault();
			loggerPersona.off('click');
			Persona(function(){
				loggerPersona.click(function(e){
					e.preventDefault();
					return navigator.id.request({
						siteName: loggerPersona.attr('data-sitename'), //Plain text name of your site to show in the login dialog. Unicode and whitespace are allowed, but markup is not.
						backgroundColor:loggerPersona.attr('data-bgcolor'),
						//oncancel:function(){}, //invoked if the user refuses to share an identity with the site.
						//privacyPolicy:'/Politique-Confidentialité', //Must be served over SSL. The termsOfService parameter must also be provided. Absolute path or URL to the web site's privacy policy. If provided, then termsOfService must also be provided. When both termsOfService and privacyPolicy are given, the login dialog informs the user that, by continuing, "you confirm that you accept this site's Terms of Use and Privacy Policy." The dialog provides links to the the respective policies.
						//returnTo: window.location, //Absolute path to send new users to after they've completed email verification for the first time. The path must begin with '/'. This parameter only affects users who are certified by Mozilla's fallback Identity Provider. This value passed in should be a valid path which could be used to set window.location too.
						//siteLogo: '/img/logo.png', //Must be served over SSL. Absolute path to an image to show in the login dialog. The path must begin with '/'. Larger images will be scaled down to fit within 100x100 pixels.
						//termsOfService: 'Termes-Utilisation', Optional Must be served over SSL. The privacyPolicy parameter must also be provided. Absolute path or URL to the web site's terms of service. If provided, then privacyPolicy must also be provided. When both termsOfService and privacyPolicy are given, the login dialog informs the user that, by continuing, "you confirm that you accept this site's Terms of Use and Privacy Policy." The dialog provides links to the the respective policies. 
					});
				});
				loggerPersona.click();
			});
			return false;
		});
	};
	dialogInitHanldle = function(){
		loadPersona();
		handleMenu();
	};
	dialogInitHanldle();
	$('a[is="login-box"]').click(function(e){
		e.preventDefault();
		if($(this).hasClass('logged'))
			return;
		if(loaded){
			dialog.dialog('open');
		}
		else{
			loadDialog();
		}
		return false;
	});
});