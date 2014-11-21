<?php
dev::level(
	//dev::CONTROL
	dev::STD
	//|dev::URI
	//|dev::MODEL	
	|dev::CSS
	|dev::JS
	//|dev::IMG
	//|dev::URI
);
class view extends surikat\view{
	protected $xDom = 'x-dom/';
	function preHooks(){
		parent::preHooks();
		i18n::setLocale('fr');
	}
}