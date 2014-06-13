<?php namespace model;
use model;
abstract class ATable_Keyword extends Table{
	static function find($find,$limit=1){
		$table = model::getClassModel(get_called_class());
		$method = $limit>1?'col':'cell';
		return model::$method($table,array(
			'select'	=>'id',
			'where'		=>'label = :equal OR label LIKE :like',
			'limit'		=>$limit,
			'order_by'	=>'created DESC',
		),array(
			'equal'	=>$find,
			'like'	=>"%$find%"
		));
	}
}
