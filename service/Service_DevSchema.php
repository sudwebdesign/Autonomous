<?php namespace service;
use ReflectionClass;
use ReflectionMethod;
use control;
class Service_DevSchema{
	static function method(){
		$class = new ReflectionClass(__CLASS__);
		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach($methods as $method)
			if($method->name!=__FUNCTION__)
				echo '<button onclick="document.location=\''.@$_SERVER['PATH_INFO'].'/'.$method->name.'\';">'.str_replace('_',' ',$method->name).'</button><br>';
	}
	static function country(){
		include control::$CWD.'/model/schema.country.php';
	}
	static function lang(){
		include control::$CWD.'/model/schema.lang.php';
	}
	static function city(){
		include control::$CWD.'/model/schema.city.php';
	}
}
