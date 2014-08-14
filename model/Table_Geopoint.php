<?php namespace model;
final class Table_Geopoint extends Table{
	use Mixin_Geopoint;
	function onValidate(){
		if(!$this->checkLat($this->lat))
			$this->error('xownGeopoint.lat','La latitude doit être comprise entre 90 et -90');
		if(!$this->checkLon($this->lon))
			$this->error('xownGeopoint.lon','La logintude doit être comprise entre 180 et -180');
		$this->setPoint($this->lat,$this->lon);
		if($this->radius)
			$this->setBounds($this->lat,$this->lon,$this->radius);
	}
}