<?php namespace Model;
use Surikat\Core\Ruler;
final class TableTag extends AbstractKeywordTable{
	protected $minLabelLength = 4;
	protected $maxLabelLength = 25;
	function onValidate(){
		parent::onValidate();
		if(!Ruler::minchar($this->name,$this->minLabelLength))
			$this->error('name','Le tag doit comporter minimum '.$this->minLabelLength.' caractères');
		elseif(!Ruler::maxchar($this->name,$this->maxLabelLength))
			$this->error('name','Le tag doit comporter maximum '.$this->maxLabelLength.' caractères');
	}
}