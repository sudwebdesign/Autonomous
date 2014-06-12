<?php
use surikat\control\session;
use model\Table_Taxonomy;
class present extends surikat\present{
	function assign(){
		$this->title				= (($u=view::param(0))?$u.' - ':'').'Autonomie et Partage';
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Évènement');
		$this->timeCompiled			= time();
	}
	function dynamic(){
		$this->time = time();
	}
}
