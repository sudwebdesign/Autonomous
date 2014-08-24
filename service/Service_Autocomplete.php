<?php namespace service;
use model;
use model\R;
use model\Query;
use control\JSON;
use control\str;
class Service_Autocomplete {
	static function geoname(){
		$results = [];
		if(isset($_GET['term'])){
			$term = trim($_GET['term']);
			$q = model::newFrom('geoname')
				->select('name')
				->select('latitude')
				->select('longitude')
				->select('radius')
				->where('fcode = ?',['ADM4'])
				->orderBy('name ASC')
			;
			if(strlen($term)>=1)
				$q->where('asciiname LIKE ?',[strtolower(str_replace('%','',str::unaccent($term)).'%')]);
			else
				$q->where('population >= ?',[6000]);
			$results = $q->getAll();
			
		}
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($results,JSON_UNESCAPED_UNICODE);
	}
	static function taxonomy(){
		//R::debug(true,2);
		$results = [];
		if(isset($_GET['term'])&&strlen($term=trim($_GET['term']))>=1){
			if(isset($_GET['name'])&&($name=trim($_GET['name']))){
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
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($results,JSON_UNESCAPED_UNICODE);
	}
	static function geoinit(){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode([
			'centerLatMainBound'=>model::getCenterLatOfMainBound(),
			'centerLngMainBound'=>model::getCenterLongOfMainBound(),
			'southWestLatMainBound'=>model::getSouthWestLatOfMainBound(),
			'southWestLngMainBound'=>model::getSouthWestLongOfMainBound(),
			'northEastLatMainBound'=>model::getNorthEastLatOfMainBound(),
			'northEastLngMainBound'=>model::getNorthEastLongOfMainBound(),
			'country'=>model::DEFAULT_COUNTRY_CODE,
			'region'=>model::DEFAULT_COUNTRY_CODE
		],JSON_UNESCAPED_UNICODE);
	}
}