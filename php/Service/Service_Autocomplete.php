<?php namespace service;
use Model\R;
use Model\Query;
use Tool\JSON;
use Tool\str;
class Service_Autocomplete {
	const DEFAULT_LG_CODE = 'fr';
	const DEFAULT_COUNTRY_CODE = 'fr';
	const DEFAULT_COUNTRY = 'France';
	const DEFAULT_DEPARTEMENT_CODE = 66;
	const CenterLatOfMainBound = 42.6012912;
	const CenterLongOfMainBound = 2.5396029999999428;
	const SouthWestLatOfMainBound = 42.333014;
	const SouthWestLongOfMainBound = 1.721635;
	const NorthEastLatOfMainBound = 42.91854;
	const NorthEastLongOfMainBound = 3.177833;
	
	protected static function getGeoname($complete=true){
		$results = [];
		if(isset($_GET['term'])){
			$term = trim($_GET['term']);
			$q = (new Query('geoname'))
				->select('name')
				->where('fcode = ?',['ADM4'])
				->orderBy('name ASC')
			;
			if($complete)
				$q
					->select('latitude')
					->select('longitude')
					->select('radius')
				;
			if(strlen($term)>=1)
				$q->where('asciiname LIKE ?',[strtolower(str_replace('%','',str::unaccent($term)).'%')]);
			else
				$q->where('population >= ?',[6000]);
			if($complete)
				$results = $q->getAll();
			else
				$results = $q->getCol();
		}
		return $results;
	}
	protected static function getTaxonomy(){
		$results = [];
		if(isset($_GET['term'])&&strlen($term=trim($_GET['term']))>=1){
			if(isset($_GET['name'])&&($name=trim($_GET['name']))){
				$name = rtrim($name,'s');
				$results = (new Query('tag'))
					->select('tag.name')
					->joinOn('taxonomy')
					->where('tag.name LIKE ? AND taxonomy.name=?',[str_replace('%','',$term).'%',$name,])
					->limit(10)
					->getCol()
				;
			};
		}
		return $results;
	}
	static function geoname(){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(self::getGeoname(),JSON_UNESCAPED_UNICODE);
	}
	static function taxonomy(){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(self::getTaxonomy(),JSON_UNESCAPED_UNICODE);
	}
	static function searchbox(){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(array_merge(self::getTaxonomy(),self::getGeoname(false)),JSON_UNESCAPED_UNICODE);
	}
	static function geoinit(){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode([
			'centerLatMainBound'=>self::CenterLatOfMainBound,
			'centerLngMainBound'=>self::CenterLongOfMainBound,
			'southWestLatMainBound'=>self::SouthWestLatOfMainBound,
			'southWestLngMainBound'=>self::SouthWestLongOfMainBound,
			'northEastLatMainBound'=>self::NorthEastLatOfMainBound,
			'northEastLngMainBound'=>self::NorthEastLongOfMainBound,
			'country'=>self::DEFAULT_COUNTRY_CODE,
			'region'=>self::DEFAULT_COUNTRY_CODE
		],JSON_UNESCAPED_UNICODE);
	}
}