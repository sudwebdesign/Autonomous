<?php namespace service;
use model;
use model\R;
use model\Query;
use control\JSON;
use control\str;
class Service_Autocomplete {
	protected static function getGeoname($complete=true){
		$results = [];
		if(isset($_GET['term'])){
			$term = trim($_GET['term']);
			$q = model::newFrom('geoname')
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
				$results = model::getCol('tag',[
					'select'=>'tag.name',
					'joinOn'=>'taxonomy',
					'where'=>['tag.name LIKE :like AND taxonomy.name=:taxonomy',[
						':like'=>str_replace('%','',$term).'%',
						':taxonomy'=>$name,
					]],
					'limit'=>10,
				]);
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
			'centerLatMainBound'=>model::CenterLatOfMainBound,
			'centerLngMainBound'=>model::CenterLongOfMainBound,
			'southWestLatMainBound'=>model::SouthWestLatOfMainBound,
			'southWestLngMainBound'=>model::SouthWestLongOfMainBound,
			'northEastLatMainBound'=>model::NorthEastLatOfMainBound,
			'northEastLngMainBound'=>model::NorthEastLongOfMainBound,
			'country'=>model::DEFAULT_COUNTRY_CODE,
			'region'=>model::DEFAULT_COUNTRY_CODE
		],JSON_UNESCAPED_UNICODE);
	}
}