<?php namespace Model;
use Model;
use Tool\str;
abstract class ATable_Keyword extends Table{
	static function findRewrite($find,&$rewrite){
		$table = R::getClassModel(get_called_class());
		$row = Model::row($table,array(
			'select'	=>array('id','name'),
			'where'		=>'LOWER(uaccent(name)) = ?',
			'limit'		=>1,
		),array(strtolower(str::unaccent($find))));
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
				$find = str::unaccent($find);
			}
			if($flag&Query::FLAG_CASE_INSENSITIVE){
				$ci .= 'LOWER(';
				$cie .= ')';
				$find = str::tolower($find);
			}
		}
		$table = R::getClassModel(get_called_class());
		$method = $limit>1?'col':'cell';
		return Model::$method($table,array(
			'select'	=>'id',
			'where'		=>$ci.'name'.$cie.' = :equal OR '.$ci.'name'.$cie.' LIKE :like',
			'limit'		=>$limit,
			'orderBy'	=>'created DESC',
		),array(
			'equal'	=>$find,
			'like'	=>"%$find%"
		));
	}
}