<?php namespace Controller;
use Dev;
use I18n;
Dev::level(
	//Dev::CONTROL
	Dev::STD
	//|Dev::URI
	//|Dev::MODEL
	|Dev::CSS
	|Dev::JS
	//|Dev::IMG
);
class Application extends \Surikat\Controller\Application{
	protected $xDom = 'x-dom/';
	function preHooks(){
		parent::preHooks();
		I18n::setLocale('fr');
	}
}
