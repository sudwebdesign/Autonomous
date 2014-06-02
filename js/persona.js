Persona = function(logged){
	//PERSONA
	localStorage.setItem('current',1);
	var currentUser = logged,
		loginBTN = $('a.login'),
		logoutBTN = $('a.logout');

	var initCALLED = false;
	var loginCALL = function(){
		initCALL();
		navigator.id.request({
			siteName: 'UDAC66', //Plain text name of your site to show in the login dialog. Unicode and whitespace are allowed, but markup is not.
			backgroundColor:'#ff9900',
			//oncancel:function(){}, //invoked if the user refuses to share an identity with the site.
			//privacyPolicy:'/Politique-Confidentialité', //Must be served over SSL. The termsOfService parameter must also be provided. Absolute path or URL to the web site's privacy policy. If provided, then termsOfService must also be provided. When both termsOfService and privacyPolicy are given, the login dialog informs the user that, by continuing, "you confirm that you accept this site's Terms of Use and Privacy Policy." The dialog provides links to the the respective policies.
			//returnTo: document.location, //Absolute path to send new users to after they've completed email verification for the first time. The path must begin with '/'. This parameter only affects users who are certified by Mozilla's fallback Identity Provider. This value passed in should be a valid path which could be used to set window.location too.
			//siteLogo: '/img/logo.png', //Must be served over SSL. Absolute path to an image to show in the login dialog. The path must begin with '/'. Larger images will be scaled down to fit within 100x100 pixels.
			//termsOfService: 'Termes-Utilisation', Optional Must be served over SSL. The privacyPolicy parameter must also be provided. Absolute path or URL to the web site's terms of service. If provided, then privacyPolicy must also be provided. When both termsOfService and privacyPolicy are given, the login dialog informs the user that, by continuing, "you confirm that you accept this site's Terms of Use and Privacy Policy." The dialog provides links to the the respective policies. 
		});
	};
	var logoutCALL = function(){
		initCALL();
		navigator.id.logout();
	};
	var logonCALL = function(currentUser){
		localStorage.setItem('currentUser',currentUser);
		loginBTN.data('origin',loginBTN.html());
		loginBTN.html(currentUser);
		//loginBTN.next('ul').removeClass('hide');
		loginBTN.after('<ul><li><a class="logout" href="javascript:;">Déconnexion</a></li><li><a href="Mon-Compte">Mon Compte</a></li></ul>');
		loginBTN.off('click',loginCALL);
	};
	var logoffCALL = function(){
		currentUser = false;
		localStorage.removeItem('currentUser');
		loginBTN.html(loginBTN.data('origin'));
		loginBTN.next('ul').addClass('hide');
		loginBTN.on('click',loginCALL);
	};
	var initCALL = function(){
		if(initCALLED)
			return;
		initCALLED = true;
		navigator.id.watch({
			onlogin: function(as){
				$.post('service/persona/login',{assertion:as},function(login){
					if(login.status==='okay')
						logonCALL(login.email);
				});
			},
			onlogout: function () {
				$.get('service/persona/logout',function(){
					logoffCALL();
				});
			},
			onready: function(){
			}
		});				
	};
	loginBTN.on('click',loginCALL);
	logoutBTN.on('click',logoutCALL);
	if(currentUser)
		logonCALL(currentUser);
	else if(!localStorage.getItem('current'))
		initCALL();
};
