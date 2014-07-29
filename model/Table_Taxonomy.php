<?php namespace model;
use model;
use control\str;
final class Table_Taxonomy extends ATable_Keyword{
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
		if($params)
			return model::getAssoc('taxonomy',array('joinOn'=>'taxonomy','where'=>array('taxonomy_taxonomy.taxonomy2_id=(SELECT id FROM taxonomy WHERE name=?)',array($params))));
		else
			return model::getAssoc('taxonomy',array('where'=>'taxonomy_id IS NULL'));
	}
}