<?php namespace Controller;
use Surikat\Config\Dev;
use Surikat\I18n\Lang;
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
	function preHooks(){
		parent::preHooks();
		Lang::set('fr');
	}
}
