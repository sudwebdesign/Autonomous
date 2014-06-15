<?php
use surikat\control\session;
use model\Table_Taxonomy;
class present extends surikat\present{
	function assign(){
		$this->timeCompiled			= time();
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Évènement');
		$this->BASE_HREF = 'http://'.$_SERVER['SERVER_NAME'].'/';
	}
	function dynamic(){
		$this->time		= time();
		$this->URI		= view::param(0);
		$this->HREF		= $this->BASE_HREF.$this->URI;
		$this->title	= (($u=view::param(0))?$u.' - ':'').'Autonomie et Partage';
		$this->h1		= $this->title;
	}
}
