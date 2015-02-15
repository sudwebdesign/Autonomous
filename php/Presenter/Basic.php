<?php namespace Presenter;
use Surikat\Tool\session;
use Model\Table;
use Model\TableTaxonomy;
use Presenter\Truncating;
use Route\Finder_ByTml;
class Basic extends \Surikat\Presenter\Presenter{
	protected $URI;
	function assign(){
		$this->URI = $this->getView()->getController()->getRouter();
		$this->HREF = $this->BASE_HREF.$this->URI[0];
		
		$x = explode('-',$this->URI[0]);
		$this->type = (isset($x[1])?$x[1]:$this->URI[0]);
		$this->mode = $x[0];
		$this->taxonomyRessource	= TableTaxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= TableTaxonomy::getChildrenbyLabel('Événement');
		$this->taxonomyAnnonce	= TableTaxonomy::getChildrenbyLabel('Annonce');
/* unbugged:: tabletaxonomy.php
				->joinShared('taxonomy')#joinOn('taxonomy')#erreur de syntaxe sur ou près de « ON »
				->select('name')#+joinShared
* */
/*	idée	$this->taxonomy[$this->mode]	= TableTaxonomy::getChildrenbyLabel($this->mode);*/
/*
		$this->taxonomy['Ressource']= TableTaxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomy['Evenement']= TableTaxonomy::getChildrenbyLabel('Événement');
		$this->taxonomy['Annonce']	= TableTaxonomy::getChildrenbyLabel('Annonce');
		var_dump('<h1>what this bullshit? chui fatiG</h1>',$this->taxonomy);exit;
		$this->taxonomyRessource	= $this->taxonomy['Ressource'];#[num]['name'];
		$this->taxonomyEvenement	= $this->taxonomy['Evenement'];#[num]['name'];
		$this->taxonomyAnnonce		= $this->taxonomy['Annonce'];#[num]['name'];
*/
	}
	function dynamic(){
		$this->prefix=Table::prefix();
		$this->URI = $this->getView()->getController()->getRouter();
		$this->title	= (($u=$this->URI[0])?$u.' - ':'').'Autonomie et Partage';
		$this->h1		= $this->title;
		#$title & $this->taxo's vars inexists in assign or dynamic in homepage
		#var_dump('<h1>zen-mode</h1>',(($u=$this->URI[0])?$u.' - ':'').'Autonomie et Partage');exit;
		#[solved] added <Presenter:Basic /> in .tml 
		#dorénavant appellé a l'accueil du site ;)	
	}
}
