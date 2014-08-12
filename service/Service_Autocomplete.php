<?php namespace service;
use model;
use model\R;
use model\Query;
use control\JSON;
use control\str;
class Service_Autocomplete {
	static function geoname(){
		$results = array();
		if(isset($_GET['term'])){
			$term = trim($_GET['term']);
			$q = model::newFrom('geoname')
				->select('name')
				->select('latitude')
				->select('longitude')
				->select('radius')
				->where('fcode = ?',array('ADM4'))
				->order_by('name ASC')
			;
			if(strlen($term)>=1)
				$q->where('asciiname LIKE ?',array(strtolower(str_replace('%','',str::unaccent($term)).'%')));
			else
				$q->where('population >= ?',array(6000));
			$results = $q->getAll();
			
		}
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($results,JSON_UNESCAPED_UNICODE);
	}
	static function taxonomy(){
		$results = array();
		if(isset($_GET['term'])&&strlen($term=trim($_GET['term']))>=1){
			if(isset($_GET['name'])&&($name=trim($_GET['name']))){
				//R::debug();
				$tags = model::getCol('tag',array(
					'joinOn'=>'taxonomy',
					'where'=>'tag.name LIKE :like AND taxonomy.name=:taxonomy',
					'limit'=>10,
				),array(
					':like'=>$term.'%',
					':taxonomy'=>$name,
				));
				$tags = array_values($tags);
				$results = array_merge($results,$tags);
			};
			
			$taxonomy = model::getCol('taxonomy',array(
				'where'=>'name LIKE ?',
				'limit'=>10,
			),array(
				$term.'%'
			));
			$taxonomy = array_values($taxonomy);
			$results = array_merge($results,$taxonomy);
		}
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($results);
	}
	static function geoinit(){
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(array(
			'centerLatMainBound'=>model::getCenterLatOfMainBound(),
			'centerLngMainBound'=>model::getCenterLongOfMainBound(),
			'southWestLatMainBound'=>model::getSouthWestLatOfMainBound(),
			'southWestLngMainBound'=>model::getSouthWestLongOfMainBound(),
			'northEastLatMainBound'=>model::getNorthEastLatOfMainBound(),
			'northEastLngMainBound'=>model::getNorthEastLongOfMainBound(),
			'country'=>model::DEFAULT_COUNTRY_CODE,
			'region'=>model::DEFAULT_COUNTRY_CODE

		));
	}
}