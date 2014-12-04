<?php namespace Controller;
use I18n\Lang;
class Application extends \Surikat\Controller\Application{
	function run($path){
		Lang::setLocale('fr');
		parent::run($path);
	}
	function filterParam($path){
		return $this->Router->filterParam($path);
	}
}
