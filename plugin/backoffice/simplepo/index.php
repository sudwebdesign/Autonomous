<?php namespace Surikat;
Rights::lock('translator');
if(isset($_GET['switch'])){
	switch($_GET['switch']){
		case 'rpc':
			header('Content-Type: application/json; charset=UTF-8;');
			echo SimplePO_JSON_RPC::Service();
		break;
		case 'synchro':
			if(!is_dir(SURIKAT_DATA.'langs/')||!is_file(SURIKAT_DATA.'langs/header.pot')){
				FS::mkdir(SURIKAT_DATA.'langs/');
				FS::dir_copy(SURIKAT_CORE.'default/langs',SURIKAT_DATA.'langs');
			}
			i18nParser::compile_all();
			echo 'OK';
		break;
		case 'clean':
			R::exec("DELETE FROM i18n_messages WHERE is_obsolete=1");
		break;
		case 'edit':
			include 'edit.html';
		break;
	}
	exit;
}
SimplePO::installer();
include 'wrap.html';
?>