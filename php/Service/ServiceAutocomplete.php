<?php namespace service;
use Model\R;
use Model\Query;
use Vars\JSON;
use Vars\STR;
class ServiceAutocomplete {
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
	
	protected static function json($r){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($r,JSON_UNESCAPED_UNICODE);
	}

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
				$q->where('asciiname LIKE ?',[strtolower(str_replace('%','',STR::unaccent($term)).'%')]);
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
		$term = isset($_GET['term'])?trim($_GET['term']):false;
		$q = (new Query('tag'))
			->select('name')
			->limit(10)
			->orderBy('created')
			->sort('DESC')
		;
		if($term)
			$q->where('name LIKE ?',[str_replace('%','',$term).'%']);
		return $q->getCol();
	}
	static function geoname(){
		self::json(self::getGeoname());
	}
	static function taxonomy(){
		self::json(self::getTaxonomy());
	}
	static function searchbox(){
		self::json(array_merge(self::getTaxonomy(),self::getGeoname(false)));
	}
	static function geoinit(){
		self::json([
			'centerLatMainBound'=>self::CenterLatOfMainBound,
			'centerLngMainBound'=>self::CenterLongOfMainBound,
			'southWestLatMainBound'=>self::SouthWestLatOfMainBound,
			'southWestLngMainBound'=>self::SouthWestLongOfMainBound,
			'northEastLatMainBound'=>self::NorthEastLatOfMainBound,
			'northEastLngMainBound'=>self::NorthEastLongOfMainBound,
			'country'=>self::DEFAULT_COUNTRY_CODE,
			'region'=>self::DEFAULT_COUNTRY_CODE
		]);
	}
}
