<?php namespace service;
use ReflectionClass;
use ReflectionMethod;
use control;
use control\CsvIterator;
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
	static function worldcitiespop_fr(){
		set_time_limit(0);
		$dir = control::$CWD.'.data/';
		$sr = ',';
		$csvIterator = new CsvIterator($dir.'worldcitiespop.csv',$sr);
		$fh = fopen($dir.'city.csv','w');
		$y = 0;
		foreach($csvIterator as $i=>$row)
			if($row[0]=='fr')
				($y+=1)&&fwrite($fh,implode($sr,$row)."\n");
		fclose($fh);
		print "$y/$i entry";
	}
	static function geoname(){
		//\model\R::wipe('geoname');
		set_time_limit(0);
		include control::$CWD.'/model/schema.geoname.php';
	}
}