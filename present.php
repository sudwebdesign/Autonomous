<?php
use surikat\control\session;
use model\Table_Taxonomy;
use present\Truncating;
class present extends surikat\present{
	function assign(){
		$this->timeCompiled			= time();
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Événement');
		$this->taxonomyAnnonce	= Table_Taxonomy::getChildrenbyLabel('Annonce');
		$port = ($_SERVER['SERVER_PORT']&&(int)$_SERVER['SERVER_PORT']!=80?':'.$_SERVER['SERVER_PORT']:'');
		$this->BASE_HREF = 'http'.(@$_SERVER["HTTPS"]=="on"?'s':'').'://'.$_SERVER['SERVER_NAME'].$port.'/';
		$this->URI		= view::getInstance()->getUri();
		$this->HREF		= $this->BASE_HREF.$this->URI[0];
	}
	function dynamic(){
		$this->time		= time();
		$this->title	= (($u=$this->URI[0])?$u.' - ':'').'Autonomie et Partage';
		$this->h1		= $this->title;
	}
}
