<?php namespace Model;
class Table extends \Surikat\Model\Table{
	static $prefix = '{#prefix}';//{$prefix} in 2.5
	static function prefix() {
		return self::$prefix;
	}
	//function onNew(){}
	//function onUpdate(){
		//$this->modified = date('Y-m-d H:i:s');
	//}
	//function onCreate(){
		//$this->created = date('Y-m-d H:i:s');
	//}
	//function onValidate(){
		//parent::onValidate();
	//}
	//function onCreated(){}
	//function onRead(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}
}
