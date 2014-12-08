<?php namespace Model;
class TableUser extends Table{
	//function onNew(){}
	//function onUpdate(){}
	function onCreate(){
		$this->created = date('Y-m-d H:i:s');
	}
	//function onValidate(){}
	//function onCreated(){}
	//function onRead(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}

}