<?php namespace Model;
use Model\Query;
use Tool\str;
final class Table_Taxonomy extends ATable_Keyword{
	function onUpdate(){
		
	}
	static function getChildrenbyLabel($params=null){
		$q = Query::getNew('taxonomy');
		if($params)
			return $q
				->joinOn('taxonomy')
				->where('taxonomy_taxonomy.taxonomy2_id=(SELECT id FROM taxonomy WHERE name=?)',[$params])
				->getAssoc()
			;
		else
			return $q
				->where('taxonomy_id IS NULL')
				->getAssoc()
			;
	}
}