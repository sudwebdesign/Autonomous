<?php namespace model;
use surikat\control\ruler;
abstract class Abstract_Table_Main extends Table{
	protected $minLabelLength = 8;
	protected $maxLabelLength = 250;
	function onValidate(){
		$l = strlen($this->titre);
		if($l<$this->minLabelLength)
			$this->errors['titre'] = 'Le titre doit comporter minimum '.$this->minLabelLength.' caractères';
		elseif($l>$this->maxLabelLength)
			$this->errors['titre'] = 'Le titre doit comporter maximum '.$this->maxLabelLength.' caractères';
		if($this->tel&&!ruler::tel($this->tel))
			$this->errors['tel'] = 'Numéro de téléphone non valide';			
		if($this->url&&!ruler::url($this->url))
			$this->errors['url'] = 'Lien non valide';
	}
}
