<?php namespace Surikat;
if(mb_substr($_SERVER['REQUEST_URI'],-1)!='/'){
	header("Status: 301 Move permanently", false, 301);
	header('Location: '.$_SERVER['REQUEST_URI'].'/');
	exit;
}
$config = include(__DIR__.'/config.php');
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>BackOffice - <?php echo $_SERVER['HTTP_HOST'];?> - SurikatFramework</title>
<link rel="SHORTCUT ICON" href="images/surikat.ico" />
<link href="jdesktop/jdesktop.css" rel="stylesheet" />
<link href="/jquery/ui.css" rel="stylesheet" />
<link href="jdesktop/forms.css" rel="stylesheet" />
<link href="jdesktop/text.css" rel="stylesheet" />
<link href="jdesktop/app.css" rel="stylesheet" />
<!--[if IE]>
<link href="jdesktop/ie-all.css" rel="stylesheet" type="text/css" />  
<![endif]-->
<!--[if lte IE 8]>
<link href="jdesktop/ie.css" rel="stylesheet" type="text/css" />  
<![endif]-->
</head>
<body>
<div id="wrapper">
	<div id="desktop">
		<div id="desktop_iconarea"></div>
	</div>
	<div id="widgets"></div>
	<div id="topmenu">
		<ul></ul>
	</div>
	<div id="taskbar">
		<a id="showdesktop" href="#" title="Show desktop">Menu</a>
		<div class="separator"></div>
		<div class="taskbarbuttons" id="taskbarbuttons"></div>
		<div class="separator"></div>
	</div>
</div>
<script src="/jquery/surikat.js"></script>
<script src="/jquery/ui.js"></script>
<script src="/jquery/ultbuttons-1-1.js"></script>
<script src="/jquery/scrollTo.js"></script>
<script src="jdesktop/jdesktop.js"></script>
<script src="jdesktop/widgets.js"></script>
<style>
.backoffice-window{
	width:99%;
	height:99%;
	padding:0;
	margin:0 auto;
	border:0;
}
.contentarea h1{
	color:#FF0000;
}
.surikat-bo-updater, .surikat-bo-updater a{
	color:#FF0000;
	text-decoration:none;
	font-size:11px;
	font-weight:bold;
	text-transform:uppercase;
}
.surikat-bo-updater{
	margin-right: 5px;
    padding: 5px;
    position: absolute;
    right: 0;
}
.surikat-bo-updater a{
	padding-left:5px;
}
</style>
<script type="application/javascript">$(document).ready(function(){	
	var handleF5 = function(e){
		if((!e.ctrlKey&&!e.metaKey&&e.which===116) || ((e.ctrlKey||e.metaKey)&&e.which===82)){ 
			var zIndex = 0;
			var select = false;
			$('#desktop div.window:visible').each(function(){
				var z = $(this).css('z-index');
				if(z>zIndex){
					zIndex = z;
					select = $(this).attr('id');
				}
			});
			if(select){
				e.preventDefault();
				var o = $('#'+select).find('iframe.backoffice-window');
				o.attr('src',o.contents().get(0).location.href);
				return false;
			}
		}
	};	
	$(document).on('keydown', handleF5);
	
	$.ajaxSetup({
		cache:false
	});
	nJDSK.init();	
	/*addMenu params: parent, id, title, href, icon, function(optional)*/
	// nJDSK.menuHelper.addMenu('','externals','Externals','#','');
	// nJDSK.menuHelper.addMenu('externals','adminer','AdminerDB','../adminer/','../images/icon-link.png');
	nJDSK.menuHelper.addMenu('','help','?','#','');
	nJDSK.menuHelper.addMenu('help','about','&Agrave; propos de Surikat','#','images/surikat.png',function(){
		$.get('./about.txt',function(about){
			nJDSK.customHeaderDialog(
					'Surikat - OpenSource-Web-FrameWork',
					'SurikatFramework<br><span style="font-size:13px;">version <?php echo SURIKAT_VERSION;?></span>',
					about,
					[
						{
							type:'ok_yes',
							value:'OK',
							callback:function(win)
							{
								win.close();
							}
						}
					],
					false,
					750,
					525
			);
		});
		return false;
	});
	var BackofficeAddIcon = function(k,v){
		nJDSK.iconHelper.addIcon(k,(typeof(v)!='undefined'&&typeof(v.label)!='undefined'?v.label:k),'jdesktop/images/'+(typeof(v)!='undefined'&&typeof(v.icon)!='undefined'?v.icon:k+'.png'),function(e){
			e.preventDefault();
			var id = 'backoffice_window_'+k;
			var msg = '<iframe id="'+id+'" class="backoffice-window" src="'+(typeof(v)!='undefined'&&typeof(v.url)!='undefined'?v.url:'./'+k)+'"></iframe>';
			var newWindow = new nJDSK.Window($(document).width()-40,$(document).height()-80,(typeof(v)!='undefined'&&typeof(v.label)!='undefined'?v.label:k),'',msg, nJDSK.uniqid());
			var iframe_window = $('#'+id).get(0).contentWindow.window;
			iframe_window.jQuery = jQuery;
			iframe_window.$ = $;
			$(document).ready(function(){
				$('#'+id).load(function(){
					var o = $(this);
					$(o.get(0).contentWindow.document).on('keydown', handleF5);
					var title = o.contents().find('head title');
					if(title.length&&title.html()){
						o.closest('div.window').find('div.titlebar span:nth-child(1)').text(title.html());
					}
					if(typeof(v.callback)=='function'){
						v.callback(o);
					}				
				});
			});
			return false;
		});
	};
	
	BackofficeAddIcon('EasyVCS',{
		url:'./easyvcs/',
		icon:'icon_easyvcs.png',
		callback:function(){
			
		}
	});
	
	<?php if(X::Config()->db_name){ ?>
	BackofficeAddIcon('ArborEscence',{
		url:'./arborescence/',
		icon:'icon_arborescence.png'
	});
	<?php } ?>
	
	BackofficeAddIcon('SimplePO',{
		url:'./simplepo/',
		icon:'icon_simplepo.png'
	});
	
	BackofficeAddIcon('Adminer',{
		icon:'icon_adminer.png',
		url:'./adminer/',
		callback:function(o){
			var iframe = o.contents();
			$('input[name="'+$.jqSelector('auth[server]')+'"]',iframe).val('<?php echo X::Config()->db_host; ?>');
			$('input[name="'+$.jqSelector('auth[username]')+'"]',iframe).val('<?php echo X::Config()->db_user; ?>');
			$('input[name="'+$.jqSelector('auth[password]')+'"]',iframe).val('<?php echo X::Config()->db_password; ?>');
			$('input[name="'+$.jqSelector('auth[db]')+'"]',iframe).val('<?php echo X::Config()->db_name; ?>');
			$('input[name="'+$.jqSelector('auth[permanent]')+'"]',iframe)
				.prop('checked',true)
				.closest('form').submit();
		}
	});
	
	BackofficeAddIcon('ImportSql',{
		url:'./sqlimport/bigdump.php',
		icon:'icon_sqlimport.png'
	});
	
	BackofficeAddIcon('phpLiteAdmin',{
		url:'./phpliteadmin/',
		icon:'icon_phpliteadmin.png'
	});

	BackofficeAddIcon('PasswordTools',{
		url:'./passwords/',
		icon:'icon_passwordtools.png'
	});	
	
	<?php if($config['agora']){ ?>
	BackofficeAddIcon('Agora',{
		url:'<?php echo $config['agora'];?>',
		icon:'icon_agora.png'
	});
	<?php } ?>
	<?php if($config['spip']){ ?>
	BackofficeAddIcon('Spip',{
		url:'<?php echo $config['spip'];?>',
		icon:'icon_spip.png'
	});
	<?php } ?>
	<?php if($config['wordpress']){ ?>
	BackofficeAddIcon('WordPress',{
		url:'<?php echo $config['wordpress'];?>',
		icon:'icon_wordpress.png'
	});
	<?php } ?>
	<?php if($config['cpanel']){ ?>
	BackofficeAddIcon('cPanel',{
		url:'<?php echo $config['cpanel'];?>',
		icon:'icon_cpanel.png'
	});
	<?php } ?>
	<?php if($config['hoster']){ ?>
	BackofficeAddIcon('Hoster',{
		url:'<?php echo $config['hoster'];?>',
		icon:'icon_hoster.png'
	});
	<?php } ?>
	
	BackofficeAddIcon('Packager',{
		url:'./packager/',
		icon:'icon_packager.png'
	});
	
	/*
	BackofficeAddIcon('CacheCleaner',{
		url:'./cachecleaner/',
		icon:'icon_cachecleaner.png'
	});
	*/
	
	BackofficeAddIcon('SeoStats',{
		url:'./seostats/',
		icon:'icon_seostats.png'
	});
	
	BackofficeAddIcon('Config',{
		url:'./config/',
		icon:'icon_config.png'
	});
	
	BackofficeAddIcon('TimeZone',{
		url:'./timezonepicker/',
		icon:'icon_timezone.png'
	});
	
	BackofficeAddIcon('PhpInfo',{
		url:'./phpinfo/',
		icon:'icon_phpinfo.png'
	});
	
	var show_surikat_update = function(msg){
		if(!$('.surikat-bo-updating').length){
			$('body').append('<div class="surikat-bo-updating"></div>');
			$('.surikat-bo-updating').dialog({
				title:"Mise à jour - Surikat",
				autoOpen:false,
				width:'70%',
				buttons: [{
					text: "OK",
					click: function(){
						$( this ).dialog( "close" );
					}
				}],
				open:function(){
					$('.surikat-bo-updater').html('Votre version est à jour');
				}
			});
		}
		$('.surikat-bo-updating').html(msg).dialog('open');
	};
	
	$('body').append('<div class="surikat-bo-updater"></div>');
	<?php if(parse_url(Updater::$update_from,PHP_URL_HOST)==parse_url(X::Config()->www,PHP_URL_HOST)){ ?>
		$('.surikat-bo-updater').append('Serveur Central WildSurikat.com');
	<?php }elseif(!is_file(Updater::$FP_LOCAL)||(filemtime(Updater::$FP_LOCAL)+(Updater::$CHECK*3600))<time()){ ?>
	$.get('/updater/?action=check',function(msg){
		if(msg=='no-changes'){
			$('.surikat-bo-updater').append('Votre version est à jour');
		}
		else if(msg=='no-connection'){
			$('.surikat-bo-updater').append('Impossible de se connecter au serveur de mises à jour');
		}
		else if(msg=='no-response'){
			$('.surikat-bo-updater').append('Pas de réponse du serveur de mises à jour');
		}
		else{
			$('.surikat-bo-updater').append('<a href="#" class="surikat-bo-update">Mise à jour vers '+msg+'</a>');
			$('a.surikat-bo-update').click(function(e){
				e.preventDefault();
				$.get('/updater/?action=upgrade',function(msg){
					show_surikat_update(msg);
				});
				return false;
			});
		}
	});
	<?php }else{ ?>
		$('.surikat-bo-updater').append('<a href="#" class="surikat-bo-force-update">Vérifier les mise à jour');
		$('a.surikat-bo-force-update').click(function(e){
			e.preventDefault();
			$.get('/updater/?action=upgrade&force=1',function(msg){
				show_surikat_update(msg);
			});
			return false;
		});
	<?php } ?>
});</script>
<a href="http://www.ubuntu.com/" style="position:fixed;bottom:22px;right:180px;z-index:1;" target="_blank"><img src="images/linux-ubuntu.png" /></a>
<a href="http://www.gnu.org/licenses/gpl.html"  style="position:fixed;bottom:30px;right:97px;z-index:1;" target="_blank"><img src="images/gnu.png" /></a>
<a href="http://php.net/" style="position:fixed;bottom:30px;right:0px;z-index:1;" target="_blank"><img src="images/php.png" /></a>
</body></html>