<?php namespace model;
final class Table_Geopoint extends Table{
	static $metaCast = array(
		//'lat'=>'float', //trying with auto (double)
		//'lng'=>'float',
		'point'=>'point', //use lat,lng in future, not before mysql5.6 or remigration to postgres
	);
	//function onNew(){}
	//function onCreate(){}
	//function onDelete(){}
	function onUpdate(){
		parent::onUpdate();
		$this->lat =(float)$this->lat;
		$this->lng =(float)$this->lng;
	}
	function onValidate(){
		parent::onValidate();
		if(!($this->lat<=90.0&&$this->lat>=-90.0))
			$this->error('lat','La latitude doit être comprise entre 90 et -90');
		if(!($this->lat<=180.0&&$this->lat>=-180.0))
			$this->error('lng','La logintude doit être comprise entre 180 et -180');
	}
	//function onCreated(){}
	//function onUpdated(){}
	//function onDeleted(){}
	//function onRead(){}
}
