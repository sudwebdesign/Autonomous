<?php
control::dev(
	//control::dev_control //just for chrono and output error
	control::dev_default //control+view+present+model
	//|control::dev_uri
	//|control::dev_model_data
	//|control::dev_model_redbean
	//|control::dev_model_sql
	|control::dev_css
	|control::dev_js
	//|control::dev_img
	//|control::dev_uri
	//|control::dev_all //very heavy
);
use control\i18n\i18n;
class view extends surikat\view{
	static $xDom = 'x-dom/';
	static function preHooks(){ #don't forget to call exit to avoid simple Template auto-Mapping when hook found
		parent::preHooks(); #automatics hooks, just /service/
		i18n::set(model::DEFAULT_LG_CODE);
		i18n::handle();
	}
	/*
	static function postHooks(){ #don't forget to call exit to avoid 404 when hook found
		if(strpos(static::$URI->getPath(),'/blog/')===0){
			control::dev(false);
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