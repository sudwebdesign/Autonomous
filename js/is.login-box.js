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
	var login = $('<div class="login-dialog" title="Login / Sign-in"></div>');
	login.appendTo('body');
	var dialog = login.dialog({
		autoOpen:false,
		modal:true,
		width:'85%'
	});
	$.get('login-box',function(html){
		dialog.append(html);
		$('[is="login-box"]').show().click(function(e){
			e.preventDefault();
			dialog.dialog('open');
			return false;
		});
	});
});