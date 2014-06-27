<?php
use surikat\control\session;
use model\Table_Taxonomy;
use present\Truncating;
class present extends surikat\present{
	function assign(){
		$this->timeCompiled			= time();
		$this->taxonomyRessource	= Table_Taxonomy::getChildrenbyLabel('Ressource');
		$this->taxonomyEvenement	= Table_Taxonomy::getChildrenbyLabel('Évènement');
		$port = ($_SERVER['SERVER_PORT']&&(int)$_SERVER['SERVER_PORT']!=80?':'.$_SERVER['SERVER_PORT']:'');
		$this->BASE_HREF = 'http://'.$_SERVER['SERVER_NAME'].$port.'/';
		$this->URI		= view::param(0);
		$this->HREF		= $this->BASE_HREF.$this->URI;
	}
	function dynamic(){
		$this->time		= time();
		$this->title	= (($u=view::param(0))?$u.' - ':'').'Autonomie et Partage';
		$this->h1		= $this->title;
	}
	static function truncatehtml($html,$lenght='20',$elipsis ='...'){
		return Truncating::truncate($html, $lenght, array('length_in_chars' => true, 'ellipsis' => $elipsis, 'xml' => true));
	}
}
