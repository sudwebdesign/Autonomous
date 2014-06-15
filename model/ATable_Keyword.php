<?php namespace model;
use model;
use control\str;
abstract class ATable_Keyword extends Table{
	static function find($find,$limit=1,$flag=model::FLAG_CASE_INSENSITIVE){
		$ci = '';
		$cie = '';
		if($flag&model::FLAG_CASE_INSENSITIVE){
			$ci = 'LOWER(';
			$cie = ')';
			$find = str::tolower($find);
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
