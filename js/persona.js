Persona = function(logged){
	//PERSONA
	localStorage.setItem('current',1);
	var currentUser = logged,
		loginBTN = $('a.login'),
		logoutBTN = $('a.logout');

	var initCALLED = false;
	var loginCALL = function(){
		initCALL();
		navigator.id.request();
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
