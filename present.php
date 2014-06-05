<?php
use surikat\control\session;
class present extends surikat\present{
	static function compileVars(&$vars=array()){
		return array(
			'title'=>(($u=view::param(0))?$u.' - ':'').'Autonomie et Partage',
			'taxonomyRessource'=>model::getTaxonomy('Ressource'),
			'taxonomyEvenement'=>model::getTaxonomy('Évènement'),
			'timeCompiled'=>time(),
		);
	}
	static function compileElement(){
		
	}	
	static function exec(){
		
	}
	static function execVars(&$vars=array()){
		return array('time'=>time());
	}
}
