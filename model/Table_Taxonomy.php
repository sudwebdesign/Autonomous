<?php namespace model;
use model;
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
			return model::col('taxonomy',array('where'=>'taxonomy_id=(SELECT id FROM taxonomy WHERE label=?)'),array($params));
		else
			return model::col('taxonomy');
	}
}
