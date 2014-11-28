<?php
Dev::level(
	//Dev::CONTROL
	Dev::STD
	//|Dev::URI
	//|Dev::MODEL	
	|Dev::CSS
	|Dev::JS
	//|Dev::IMG
	//|Dev::URI
);
class View extends Surikat\View{
	protected $xDom = 'x-dom/';
	function preHooks(){
		parent::preHooks();
		I18n::setLocale('fr');
	}
}