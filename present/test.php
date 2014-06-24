<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
class test extends \present{
	function assign(){
		$this->title = 'test';
		$this->foo = 'bar';
	}
	function dynamic(){
		$this->test = 'ok';
		$this->time = 0;
	}
	function sayHello(){
		return 'Hello me is Test';
	}
}
