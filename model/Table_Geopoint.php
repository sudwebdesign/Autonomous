<?php namespace model;
final class Table_Geopoint extends Table{
	static $metaCast = array(
		//'lat'=>'float', //trying with auto (double)
		//'lng'=>'float',
		'point'=>'point', //use lat,lng in future, not before mysql5.6 or remigration to postgres
	);
	//function onValidate(){
		//parent::onValidate();
	//}
	//function onNew(){}
	//function onCreate(){}
	//function onCreated(){}
	//function onRead(){}
	//function onUpdate(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}
}
