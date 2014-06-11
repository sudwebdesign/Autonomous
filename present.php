<?php
use surikat\control\session;
use model\Table_Taxonomy;
class present extends surikat\present{
	static function assign($o){
		$o->title				= (($u=view::param(0))?$u.' - ':'').'Autonomie et Partage';
		$o->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$o->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Évènement');
		$o->timeCompiled		= time();
	}
	static function dynamic($o){
		$o->time = time();
	}
}
