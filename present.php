<?php
use surikat\control\session;
use model\Table_Taxonomy;
class present extends surikat\present{
	function assign(){
		$this->timeCompiled			= time();
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Évènement');
		$this->BASE_HREF = 'http://'.$_SERVER['SERVER_NAME'].'/';
		$this->URI = view::param(0);
		$this->HREF = $this->BASE_HREF.$this->URI;
		$this->title				= (($u=view::param(0))?$u.' - ':'').'Autonomie et Partage';
	}
	function dynamic(){
		$this->time = time();
	}
}
