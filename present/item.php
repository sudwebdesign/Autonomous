<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
class item extends \present{
	static $taxonomy;
	static function compileVars(&$vars=array()){
		
	}
	static function compileElement(){
		
	}
	static function exec(){
		static::$taxonomy = end(self::$options['namespaces']);
	}	
	static function execVars(&$vars=array()){
		$table = static::$taxonomy;
		
		return array(
			'title'=>view::param(1),
			'row'=>model::row4D(static::$taxonomy,array('where'=>static::$taxonomy.'.id=?'),array(view::param(2))),
			'taxonomy'=>static::$taxonomy,
		);
	}
}
