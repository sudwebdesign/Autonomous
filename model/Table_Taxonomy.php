<?php namespace model;
use model;
use model\cache;
final class Table_Taxonomy extends Table{
	function onUpdate(){
		
	}
	//function onValidate(){
		//parent::onValidate();
	//}
	//function onNew(){}
	//function onCreate(){}
	//function onCreated(){}
	//function onRead(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}
	static function getChildrenbyLabel($params=null){
		model::schemaAuto('taxonomy');
		if($params)
			return cache::syncCol('taxonomy',array('where'=>'taxonomy_id=(SELECT id FROM taxonomy WHERE label=?)'),array($params));
		else
			return cache::syncCol('taxonomy');
	}
}
