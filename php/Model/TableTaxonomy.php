<?php namespace Model;
use Model\Query;
final class TableTaxonomy extends AbstractKeywordTable{
	static function prefix() {
		parent::prefix();
	}
	function onUpdate(){
		
	}
	static function getChildrenbyLabel($params=null){
		$q = new Query('taxonomy');
		if($params)
			return $q
				->select('name')#+joinShared
				->joinOwn('taxonomy_taxonomy')
#				->joinShared('taxonomy')#


#->joinOn('taxonomy')#erreur de syntaxe sur ou près de « ON » [old)
				->where(self::prefix().'taxonomy_taxonomy.taxonomy2_id=(SELECT id FROM '.self::prefix().'taxonomy WHERE name=?)',[$params])
				->getAssoc()
			;
		else
			return $q
				->where(self::prefix().'taxonomy_id IS NULL')
				->getAssoc()
			;
	}
}
