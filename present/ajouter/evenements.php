<?php namespace present\ajouter;
use model;
use model\R;
final class evenements extends \present\ajouter{
	static function exec(){
		parent::POST('evenements');
	}
	protected static function POST($bean){
		exit(print('<pre>'.print_r($bean->getArray(),true)));
		R::store($bean);
	}
}
