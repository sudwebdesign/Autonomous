<?php namespace service;
use model;
use model\R;
use control\JSON;
class Service_Autocomplete {
	const URL_ADRESS_OSM = 'http://nominatim.openstreetmap.org/search.php?q=%s+%s&format=json';
	const URL_ADRESS_GOOGLE_HACK = 'https://maps.google.com/maps/suggest?q=%s,%s';
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
	static function adresse(){
		$results = array();
		if(isset($_GET['term']))
			//$results = self::getAdressesSuggestionsFromOSM($_GET['term']);
			$results = self::getAdressesSuggestionsFromGoogle($_GET['term']);
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode((array)$results);
	}
	static function searchbox(){
		$results = array();
		if(isset($_GET['term'])&&strlen($term=trim($_GET['term']))>=1){			
			$taxonomy = model::col('taxonomy',array(
				'where'=>'name LIKE ?',
				'limit'=>10,
			),array(
				$term.'%'
			));
			$taxonomy = array_values($taxonomy);
			$results = array_merge($results,$taxonomy);
			$results = array_merge($results,self::getKeywordSuggestionsFromGoogle($term));
		}
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($results);
	}
	static function taxonomy(){
		$results = array();
		if(isset($_GET['term'])&&strlen($term=trim($_GET['term']))>=1){
			if(isset($_GET['name'])&&($name=trim($_GET['name']))){
				//R::debug();
				$tags = model::col('tag',array(
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
			
			$taxonomy = model::col('taxonomy',array(
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
	private static function getAdressesSuggestionsFromOSM($term) {
		if(strlen($_GET['term'])<2)
			return;
		$results = array();
		$term = str_replace(array('-','!','?','_',':','.',','),' ',$term);
			$term = str_replace(array(
				' les ',
				' le ',
				' de ',
				' en ',
				' sur ',
			),' ',' '.$term.' ');
			$term = trim($term);
			$url = sprintf(self::URL_ADRESS_OSM,urlencode($term),urlencode(model::getDepartementName().'+'.model::DEFAULT_COUNTRY));
			foreach((array)json_decode(file_get_contents($url)) as $r)
				$results[] = $r->display_name;
		return $results;
	}
	private static function getAdressesSuggestionsFromGoogle($term) {
		if(strlen($_GET['term'])<5)
			return;
		$results = array();
		$suff = model::getDepartementName().', '.model::DEFAULT_COUNTRY;
		$url = sprintf(self::URL_ADRESS_GOOGLE_HACK,urlencode($term),urlencode($suff));
		$json = JSON::decode(file_get_contents($url));
		$lsuff = strlen($suff)*-1;
		if(is_object($json)&&isset($json->suggestion)&&is_array($json->suggestion))
			foreach($json->suggestion as $suggest){
				if(substr($suggest->query,$lsuff)==$suff){
					$suggest->query = substr($suggest->query,0,$lsuff-2); //-2 is for strip ", "
					$results[] = $suggest->query;
				}
			}
		return $results;
	}
	private static function getKeywordSuggestionsFromGoogle($keyword) {
		$keywords = array();
		$data = file_get_contents('http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en-US&q='.urlencode($keyword));
		if (($data = json_decode($data, true))!==null)
			$keywords = $data[1];
		return $keywords;
	}
}