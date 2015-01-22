<?php namespace Model;
use Model\Query;
use Core\STR;
abstract class ATableKeyword extends Table{
	protected static $loadUniq = 'name';
	static function find($find,$limit=1){
		$table = R::getClassModel(get_called_class());
		$method = $limit>1?'getCol':'getCell';
		return (new Query($table))
			->select('id')
			->where('name = ? OR name LIKE ?',[$find,"%$find%"])
			->limit($limit)
			->orderBy('created DESC')
			->$method()
		;
	}
}