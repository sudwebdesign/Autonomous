<?php namespace Model;
class Table_User extends Table{
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