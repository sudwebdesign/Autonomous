<?php namespace model;
final class Table_Evenements extends \surikat\model\Table{
	#<workflow CRUD>
	private $minLabelLength = 8;
	function onValidate(){
		if(strlen($this->label)<$this->minLabelLength){
			$this->errors['label'] = 'Le titre doit comporter minimum '.$this->minLabelLength.' caractères';
		}
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
