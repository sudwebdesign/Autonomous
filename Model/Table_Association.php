<?php namespace Model;
final class Table_Association extends ATable_Main{
	
	function onValidate(){
		parent::onValidate();
		if(strlen($this->raison)>200)
			$this->error('raison','Votre dépasse 200');
	}
	//function onNew(){}
	//function onCreate(){}
	//function onCreated(){}
	//function onRead(){}
	//function onUpdate(){}
	//function onUpdated(){}
	//function onDelete(){}
	//function onDeleted(){}
}
