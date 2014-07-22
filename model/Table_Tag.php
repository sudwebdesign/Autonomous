<?php namespace model;
use surikat\control\ruler;
final class Table_Tag extends ATable_Keyword{
	protected $minLabelLength = 4;
	protected $maxLabelLength = 25;
	function onValidate(){
		parent::onValidate();
		if(!ruler::minchar($this->label,$this->minLabelLength))
			$this->error('label','Le label doit comporter minimum '.$this->minLabelLength.' caractères');
		elseif(!ruler::maxchar($this->label,$this->maxLabelLength))
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