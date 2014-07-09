<?php namespace model;
use model;
use control\str;
abstract class ATable_Keyword extends Table{
	static function findRewrite($find,&$rewrite){
		$table = model::getClassModel(get_called_class());
		$row = model::row($table,array(
			'select'	=>array('id','label'),
			'where'		=>'LOWER(uaccent(label)) = ?',
			'limit'		=>1,
		),array(strtolower(str::unaccent($find))));
		if(!$row)
			return;
		if($row['label']!=$find)
			$rewrite = $row['label'];
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
		$table = model::getClassModel(get_called_class());
		$method = $limit>1?'col':'cell';
		return model::$method($table,array(
			'select'	=>'id',
			'where'		=>$ci.'label'.$cie.' = :equal OR '.$ci.'label'.$cie.' LIKE :like',
			'limit'		=>$limit,
			'order_by'	=>'created DESC',
		),array(
			'equal'	=>$find,
			'like'	=>"%$find%"
		));
	}
}