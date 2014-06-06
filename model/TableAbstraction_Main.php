<?php namespace model;
use surikat\control\ruler;
abstract class TableAbstraction_Main extends Table{
	protected $minTitreLength = 8;
	protected $maxTitreLength = 250;
	function onValidate(){
		if(!ruler::minchar($this->titre,$this->minTitreLength))
			$this->error('titre','Le titre doit comporter minimum '.$this->minTitreLength.' caractères');
		elseif(!ruler::maxchar($this->titre,$this->maxTitreLength))
			$this->error('titre','Le titre doit comporter maximum '.$this->maxTitreLength.' caractères');
		if($this->tel&&!ruler::tel($this->tel))
			$this->error('tel','Numéro de téléphone non valide');
		if($this->url&&!ruler::url($this->url))
			$this->error('url','Lien non valide');
	}
}
