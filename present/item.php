<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
class item extends \present{
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);
	}
	function dynamic(){
		parent::dynamic();
		$this->title = view::param(1);
		$t = $this->taxonomy;
		$query = array(
			'where'=>$t.'.id=?'
		);
		$params = array(
			view::param(2),
		);
		$this->raw = model::row4D($t,$query,$params);
		$imgFolder = 'content/'.$this->taxonomy.'/'.view::param(2).'/';
		$imgName=str_replace(' ','-',$this->title);
		$imgsItem = glob($imgFolder."{".$imgName.".jpg,".$imgName.".gif,".$imgName.".png}", GLOB_BRACE);
		$this->srcimg = $imgsItem;
		$this->presentation = $this->raw['presentation'];
		$this->tel = $this->raw['tel'];
		$this->lien = $this->raw['url'];
		$this->création = $this->raw['created'];
	}
}
