<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
class item extends \present{
	function assign(){
		parent::assign();
	}
	function dynamic(){
		parent::dynamic();
		$this->taxonomy = end($this->presentNamespaces);
		$this->title = view::param(1);
		$t = $this->taxonomy;
		$query = array(
			'where'=>$t.'.id=?'
		);
		$params = array(
			view::param(2),
		);
		$this->row = model::row4D($t,$query,$params);
	}
}
