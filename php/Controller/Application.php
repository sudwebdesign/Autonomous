<?php namespace Controller;
use I18n\Lang;
class Application extends \Surikat\Controller\Application{
	function setHooks(){
		setlocale (LC_ALL, 'fr', 'fr_FR', 'fr_FR.utf8', 'fra');
		Lang::set('fr');
	}
	function filterParam($path){
		return $this->Router->filterParam($path);
	}
}
