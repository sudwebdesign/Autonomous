<?php namespace model;
use surikat\control\ruler;
abstract class TableAbstraction_Main extends Table{
	protected $minTitreLength = 8;
	protected $maxTitreLength = 250;
	function onValidate(){
		$l = strlen($this->titre);
		if($l<$this->minTitreLength)
			$this->errors['titre'] = 'Le titre doit comporter minimum '.$this->minTitreLength.' caractères';
		elseif($l>$this->maxTitreLength)
			$this->errors['titre'] = 'Le titre doit comporter maximum '.$this->maxTitreLength.' caractères';
		if($this->tel&&!ruler::tel($this->tel))
			$this->errors['tel'] = 'Numéro de téléphone non valide';			
		if($this->url&&!ruler::url($this->url))
			$this->errors['url'] = 'Lien non valide';
	}
}
