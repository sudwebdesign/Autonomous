<?php
use surikat\control;
use surikat\model\W;
class model extends surikat\model{	
	const DEFAULT_COUNTRY_CODE = 'fr';
	const DEFAULT_COUNTRY = 'France';
	const DEFAULT_DEPARTEMENT_CODE = 66;
	static function getCommunesByDefDpt(){
		return W::col('communes',array('join'=>"JOIN departements ON departements.id=communes.departements_id AND departements.code=?"),array(self::DEFAULT_DEPARTEMENT_CODE));
	}
	static function getCommunesByDefDptGeo(){// see redbeanphp doc http://www.redbeanphp.com/querying
		//return W::table('communes',array('join'=>"JOIN departements ON departements.id=communes.departements_id AND departements.code=?"),array(self::DEFAULT_DEPARTEMENT_CODE));
		//return W::col('communes',array('where'=>"communes.departements_id=67"),array(self::DEFAULT_DEPARTEMENT_CODE));// no lat && lon, it's col()
		//<3  ---Syntax error or access violation: 1064--- return W::col('communes',array('select'=>"SELECT communes.id,communes.code,communes.label,communes.altitude,communes.lat,communes.lon",'where'=>"communes.departements_id=67"),array(self::DEFAULT_DEPARTEMENT_CODE));// no lat && lon, it's col()
		//return W::col('communes',array('select'=>"*",'where'=>"communes.departements_id=67"),array(self::DEFAULT_DEPARTEMENT_CODE));// no lat && lon, it's col()
		
		//all city of languedoc-R. region
		//return W::table('communes',array('select'=>'label,code,lat,lon','where'=>'communes.regions_id=24'),array(self::DEFAULT_DEPARTEMENT_CODE));// no lat && lon, it's col()

		return W::table('communes',array('select'=>'label,lat,lon','join'=>"JOIN departements ON departements.id=communes.departements_id AND departements.code=?"),array(self::DEFAULT_DEPARTEMENT_CODE));// no lat && lon, it's col()

//not work but not bug, return: array 0  (c moi ki savai po m'servir du truc)
		//return W::col('communes',array('select'=>'communes.id,communes.code,communes.label,communes.altitude,communes.lat,communes.lon'),array('where'=>"communes.departements_id=67"),array(self::DEFAULT_DEPARTEMENT_CODE));


	}
	static function getDepartementName(){
		/*put auto schema*/
		return W::cell('departements',array('where'=>'code = ?'),array(func_num_args()?func_get_arg(0):self::DEFAULT_DEPARTEMENT_CODE));
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
	static function getTaxonomy($params=null){
		self::schemaAuto('taxonomy');
		if($params)
			return self::col('taxonomy',array('where'=>'taxonomy_id=(SELECT id FROM taxonomy WHERE label=?)'),array($params));
		else
			return self::col('taxonomy');
	}
	static function liste($tb,$rowByPage=20,$paginationOffset=0){
		$themes =  W::col('themes');
		$communes = W::col('communes',array('join'=>"JOIN departements ON departements.id=communes.departements_id AND departements.code=?"),array(self::DEFAULT_DEPARTEMENT_CODE));
		//$relationalQuerySum[] = ;
		$relationalQuerySum = implode(' && ',$relationalQuerySum);
		return W::table4D($tb,array(
			'sum'=>array(/*$tb.'.id IN('.$id.')'*/),
			'limit'=>$rowByPage,
			'offset'=>$paginationOffset
		));
	}
	static function rowsTotal($tb='evenements'){
		$relationalQuerySum = array();
		$joinOn = array();
		return W::count($tb,array('sum'=>$relationalQuerySum,'joinOn'=>$joinOn));
	}
}
