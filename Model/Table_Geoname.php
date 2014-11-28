<?php namespace Model;
final class Table_Geoname extends Table{	
	static $columnPointCast	= 'point';
	//var $columnPointWriteCol = 'GeomFromText'; //MySql
	//var $columnPointReadCol	= 'asText'; //MySql
	function onValidate(){
		parent::onValidate();
		$this->latitude = $this->latitude!=''?(float)$this->latitude:false;
		$this->longitude = $this->longitude!=''?(float)$this->longitude:false;
		if($this->latitude<=90.0&&$this->latitude>=-90.0&&$this->longitude<=180.0&&$this->longitude>=-180.0)
			//$this->point = 'POINT('.$this->latitude.' '.$this->longitude.')'; //MySql
			$this->point = '('.$this->latitude.','.$this->longitude.')'; //PgSql
	}
}