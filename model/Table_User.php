<?php namespace model;
class Table_User extends Table{
	static $columnEmailReadCol = 'uaccent';
	static $columnEmailTestReadCol = 'uaccent';
	//function onNew(){
	//}
	//function onUpdate(){}
	function onCreate(){
		$this->created = date('Y-m-d H:i:s');
	}
	//function onValidate(){
		//parent::onValidate();
	//}
	//function onCreated(){}
	//function onRead(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}

}