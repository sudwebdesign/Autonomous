<?php namespace Model;
use Surikat\Tool\ruler;
final class Table_Tag extends ATable_Keyword{
	protected $minLabelLength = 4;
	protected $maxLabelLength = 25;
	function onValidate(){
		parent::onValidate();
		if(!ruler::minchar($this->name,$this->minLabelLength))
			$this->error('sharedTag.name','Le tag doit comporter minimum '.$this->minLabelLength.' caractères');
		elseif(!ruler::maxchar($this->name,$this->maxLabelLength))
			$this->error('sharedTag.name','Le tag doit comporter maximum '.$this->maxLabelLength.' caractères');
	}
}