<?php namespace Presenter;
use Surikat\Tool\session;
use Model\Table_Taxonomy;
use Presenter\Truncating;
use Route\Finder_ByView;
class Basic extends \Surikat\Presenter\Basic{
	function assign(){
		$this->timeCompiled			= time();
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Événement');
		$this->taxonomyAnnonce	= Table_Taxonomy::getChildrenbyLabel('Annonce');
		$port = ($_SERVER['SERVER_PORT']&&(int)$_SERVER['SERVER_PORT']!=80?':'.$_SERVER['SERVER_PORT']:'');
		$this->BASE_HREF = 'http'.(@$_SERVER["HTTPS"]=="on"?'s':'').'://'.$_SERVER['SERVER_NAME'].$port.'/';
		$this->URI		= Finder_ByView::getInstance();
		$this->HREF		= $this->BASE_HREF.$this->URI[0];
	}
	function dynamic(){
		$this->time		= time();
		$this->title	= (($u=$this->URI[0])?$u.' - ':'').'Autonomie et Partage';
		$this->h1		= $this->title;
	}
}
