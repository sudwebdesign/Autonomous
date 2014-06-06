<?php namespace model;
use surikat\control\ruler;
final class Table_Tag extends Table{
	protected $minLabelLength = 8;
	protected $maxLabelLength = 25;
	function onValidate(){
		parent::onValidate();
		if(!ruler::minchar($this->label,$this->minLabelLength))
			$this->error('label','Le label doit comporter minimum '.$this->minLabelLength.' caractères');
		elseif(!ruler::minchar($this->label,$this->maxLabelLength))
			$this->error('label','Le label doit comporter maximum '.$this->maxLabelLength.' caractères');
	}
	//function onNew(){}
	//function onCreate(){}
	//function onCreated(){}
	//function onUpdate(){}
	//function onRead(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}
}
