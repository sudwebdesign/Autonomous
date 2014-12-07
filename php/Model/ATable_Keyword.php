<?php namespace Model;
use Model\Query;
use Core\STR;
abstract class ATable_Keyword extends Table{
	static function findRewrite($find,&$rewrite){
		$table = R::getClassModel(get_called_class());
		$row = (new Query($table))
			->select('id')
			->select('name')
			->where('LOWER(uaccent(name)) = ?',[strtolower(STR::unaccent($find))])
			->limit(1)
			->getRow()
		;
		if(!$row)
			return;
		if($row['name']!=$find)
			$rewrite = $row['name'];
		return $row['id'];
	}
	static function find($find,$limit=1,$flag=0){
		$ci = '';
		$cie = '';
		if($flag){
			if($flag&Query::FLAG_ACCENT_INSENSITIVE){
				$ci .= 'uaccent(';
				$cie .= ')';
				$find = STR::unaccent($find);
			}
			if($flag&Query::FLAG_CASE_INSENSITIVE){
				$ci .= 'LOWER(';
				$cie .= ')';
				$find = STR::tolower($find);
			}
		}
		$table = R::getClassModel(get_called_class());
		$method = $limit>1?'getCol':'getCell';
		return (new Query($table))
			->select('id')
			->where($ci.'name'.$cie.' = ? OR '.$ci.'name'.$cie.' LIKE ?',[$find,"%$find%"])
			->limit($limit)
			->orderBy('created DESC')
			->$method()
		;
	}
}