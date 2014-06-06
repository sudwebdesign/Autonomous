<?php
use surikat\control\session;
use model\Table_Taxonomy;
class present extends surikat\present{
	static function compileVars(&$vars=array()){
		return array(
			'title'=>(($u=view::param(0))?$u.' - ':'').'Autonomie et Partage',
			'taxonomyRessource'=>Table_Taxonomy::getChildrenbyLabel('Ressource'),
			'taxonomyEvenement'=>Table_Taxonomy::getChildrenbyLabel('Évènement'),
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
