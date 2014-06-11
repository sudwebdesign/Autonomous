<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
class item extends \present{
	static $taxonomy;
	static function assign($o){
		
	}
	static function dynamic($o){
		$o->taxonomy = end($o->options->namespaces);
		$o->title = view::param(1);
		$t = $o->taxonomy;
		$query = array(
			'where'=>$t.'.id=?'
		);
		$params = array(
			view::param(2),
		);
		$o->row = model::row4D($t,$query,$params);
	}
}
