<?php
dev::level(
	//dev::CONTROL //just for chrono and output error
	dev::STD
	//|dev::URI
	//|dev::MODEL	
	|dev::CSS
	|dev::JS
	//|dev::IMG
	//|dev::URI
	//|dev::ALL //very heavy
);
use control\i18n\i18n;
class view extends surikat\view{
	static $xDom = 'x-dom/';
	static function preHooks(){ #don't forget to call exit to avoid simple Template auto-Mapping when hook found
		parent::preHooks(); #automatics hooks, just /service/
		i18n::setLocale('fr');
	}
	/*
	static function postHooks(){ #don't forget to call exit to avoid 404 when hook found
		if(strpos(static::$URI->getPath(),'/blog/')===0){
			dev::level(dev::NO);
			//if(strpos(static::$PATH,'/blog/wp-admin/')===0)
				//include('plugin/wordpress/wp-admin/index.php');
			//else
				include('plugin/wordpress/index.php');
			exit;
		}
		
	}
	*/
	/* //add here your jquery-like manipulation on dom before compile
	static function document($TML){
		parent::document($TML); #register "present:" in tml templates & auto min when PROD
		
	}
	*/
}