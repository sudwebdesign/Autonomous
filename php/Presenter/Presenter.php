<?php namespace Presenter;
class Presenter extends \Surikat\Presenter\Presenter{
	function assign(){
		$this->URI = $this->getView()->getController()->getRouter();
		$this->HREF = $this->BASE_HREF.$this->URI[0];		
		$x = explode('-',$this->URI[0]);
		$this->type = (isset($x[1])?$x[1]:$this->URI[0]);
		$this->mode = $x[0];
	}
	function dynamic(){
		$this->URI = $this->getView()->getController()->getRouter();
		$this->title	= (($u=$this->URI[0])?$u.' - ':'').'Autonomous';
		$this->h1		= $this->title;
	}
}