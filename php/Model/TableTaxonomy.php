<?php namespace Model;
use Model\Query;
final class TableTaxonomy extends AbstractKeywordTable{
	function onUpdate(){
		
	}
	static function getChildrenbyLabel($params=null){
		$q = new Query('taxonomy');
		if($params)
			return $q
				->joinOn('taxonomy')
				->where('{$prefix}taxonomy_taxonomy.taxonomy2_id=(SELECT id FROM {$prefix}taxonomy WHERE name=?)',[$params])
				->getAssoc()
			;
		else
			return $q
				->where('{$prefix}taxonomy_id IS NULL')
				->getAssoc()
			;
	}
}