<?php namespace model;
final class Table_Geopoint extends Table{	
	static $columnPointCast	= 'point';
	//var $columnPointWriteCol	= 'GeomFromText'; //MySql
	//var $columnPointReadCol	= 'asText'; //MySql
	//function onNew(){}
	//function onCreate(){}
	//function onDelete(){}
	function onUpdate(){
		parent::onUpdate();
		$this->lat = $this->lat!=''?(float)$this->lat:false;
		$this->lon = $this->lon!=''?(float)$this->lon:false;
	}
	function onValidate(){
		parent::onValidate();
		if($this->lat!==false&&!($this->lat<=90.0&&$this->lat>=-90.0))
			$this->error('xownGeopoint.lat','La latitude doit être comprise entre 90 et -90');
		if($this->lon!==false&&!($this->lon<=180.0&&$this->lon>=-180.0))
			$this->error('xownGeopoint.lon','La logintude doit être comprise entre 180 et -180');
		//$this->point = 'POINT('.$this->lat.' '.$this->lon.')'; //MySql
		$this->point = '('.$this->lat.','.$this->lon.')'; //PgSql
	}
	//function onCreated(){}
	//function onUpdated(){}
	//function onDeleted(){}
	//function onRead(){}
}