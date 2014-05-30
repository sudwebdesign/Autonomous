<?php namespace model;
final class Table_Evenements extends \surikat\model\Table{
	#<workflow CRUD>
	private $minLabelLength = 8;
	private $maxLabelLength = 250;
	function onValidate(){
		$l = strlen($this->titre);
		if($l<$this->minLabelLength)
			$this->errors['titre'] = 'Le titre doit comporter minimum '.$this->minLabelLength.' caractères';
		elseif($l>$this->maxLabelLength)
			$this->errors['titre'] = 'Le titre doit comporter maximum '.$this->maxLabelLength.' caractères';
	}
	function onCreate(){}
	function onCreated(){}
	function onRead(){}
	function onUpdate(){}
	function onUpdated(){}
	function onDelete(){}
	function onDeleted(){}
	#</workflow>
}
?>
