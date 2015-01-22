$css('is.login-box');
$js('jquery',function(){
	$('[is="login-box"]').show();
	$.get('login-box',function(html){
		$('body').append('<div class="login-popup"><a href="#" class="close"><img src="img/close_pop.png" class="btn_close" title="Close Window" alt="Close"></a>'+html+'</div><div class="mask"></div>')
		$('header a[href="Login"]').click(function(e){
			e.preventDefault();
			$('.login-popup').fadeIn(300);
			var popMargTop = ($('.login-popup').height() + 24) / 2; 
			var popMargLeft = ($('.login-popup').width() + 24) / 2; 
			$('.login-popup').css({ 
				'margin-top' : -popMargTop,
				'margin-left' : -popMargLeft
			});
			$('.mask').fadeIn(300);
			return false;
		});
		$('a.close, .mask').click(function(){
			$('.mask , .login-popup').fadeOut(300);
			return false;
		});
	});
});