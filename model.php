<?php
use surikat\control;
//use surikat\model\cache;
use surikat\model as cache; //disable hard for dev
class model extends surikat\model{	
	const DEFAULT_LG_CODE = 'fr';
	const DEFAULT_COUNTRY_CODE = 'fr';
	const DEFAULT_COUNTRY = 'France';
	const DEFAULT_DEPARTEMENT_CODE = 66;
	static function getCommunesByDefDpt(){
		return cache::col('locality',array('join'=>"JOIN geoarealevel2 ON geoarealevel2.id=locality.geoarealevel2_id AND geoarealevel2.code=?"),array(self::DEFAULT_DEPARTEMENT_CODE));
	}
	static function getDepartementName(){
		return cache::cell('geoarealevel2',array('where'=>'code = ?'),array(func_num_args()?func_get_arg(0):self::DEFAULT_DEPARTEMENT_CODE));
	}
	static function getCenterLatOfMainBound(){
		return 42.6012912;
	}
	static function getCenterLongOfMainBound(){
		return 2.5396029999999428;
	}
	static function getSouthWestLatOfMainBound(){
		return 42.333014;
	}
	static function getSouthWestLongOfMainBound(){
		return 1.721635;
	}
	static function getNorthEastLatOfMainBound(){
		return 42.91854;
	}
	static function getNorthEastLongOfMainBound(){
		return 3.177833;
	}
}
