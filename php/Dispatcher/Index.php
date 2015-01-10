<?php namespace Dispatcher;
use I18n\Lang;
class Index extends \Surikat\Dispatcher\Index{
	function setHooks(){
		setlocale(LC_ALL, 'fr', 'fr_FR', 'fr_FR.utf8', 'fra');
		Lang::set('fr');
	}
}
