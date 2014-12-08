<?php namespace Model;
final class TableGeopoint extends Table{
	use Mixin_Geopoint;
	function onValidate(){
		if(false===($this->lat=$this->checkLat($this->lat)))
			$this->error('xownGeopoint.lat','La latitude doit être comprise entre 90 et -90');
		if(false===($this->lon=$this->checkLon($this->lon)))
			$this->error('xownGeopoint.lon','La longitude doit être comprise entre 180 et -180');
		$this->setPoint($this->lat,$this->lon);
		if($this->radius){
			$this->radius = (float)$this->radius;
			$this->setBounds($this->lat,$this->lon,$this->radius);
		}
	}
}