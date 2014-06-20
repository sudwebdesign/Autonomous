<?php namespace model;
final class Table_Geopoint extends Table{
	static $metaCast = array(
		//'lat'=>'float', //trying with auto (double)
		//'lng'=>'float',
		//'point'=>'point',
	);
	//function onNew(){}
	//function onCreate(){}
	//function onDelete(){}
	function onUpdate(){
		parent::onUpdate();
		$this->lat = $this->lat!=''?(float)$this->lat:false;
		$this->lng = $this->lng!=''?(float)$this->lng:false;
	}
	function onValidate(){
		parent::onValidate();
		if($this->lat!==false&&!($this->lat<=90.0&&$this->lat>=-90.0))
			$this->error('lat','La latitude doit être comprise entre 90 et -90');
		if($this->lng!==false&&!($this->lng<=180.0&&$this->lng>=-180.0))
			$this->error('lng','La logintude doit être comprise entre 180 et -180');
		//$this->point = 'POINT('.$this->lat.' '.$this->lng.')';
	}
	//function onCreated(){}
	//function onUpdated(){}
	//function onDeleted(){}
	//function onRead(){}
}
