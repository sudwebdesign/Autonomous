<?php namespace Presenter;
use Surikat\Config\Domain;
use Surikat\Tool\session;
use Model\Table_Taxonomy;
use Presenter\Truncating;
use Route\Finder_ByTml;
class Basic extends \Surikat\Presenter\Basic{
	function assign(){
		$this->URI = $this->View->get('URI');
		$this->timeCompiled			= time();
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Événement');
		$this->taxonomyAnnonce	= Table_Taxonomy::getChildrenbyLabel('Annonce');
		$this->BASE_HREF = Domain::getBaseHref();
		$this->HREF = $this->BASE_HREF.$this->URI[0];
	}
	function dynamic(){
		$this->URI = $this->View->get('URI');
		$this->time		= time();
		$this->title	= (($u=$this->URI[0])?$u.' - ':'').'Autonomie et Partage';
		$this->h1		= $this->title;
	}
}
