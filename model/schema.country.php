<?php namespace model;
use control;
use control\CsvIterator;
$d = control::$CWD.'/.data/';
$countries = new CsvIterator($d.'country.csv',';');
$allCols = array(
	'label',
	'nameEn',
	'nameFr',
	'nameEs',
	'nameIt',
	'nameDe',
	'nameRu',
	'nameEnReading',
	'nameFrReading',
	'nameRuReading',
);
$orderCols = array(
	0,
	3,
	7,
	9,
	25,
	26,
	13,
	4,
	8,
	14
	
);
$countries->setKeys(array_combine($orderCols,$allCols));
$missingCols = array();
$completesCols = array();
foreach($countries as $i=>$data){
	print_r($data);
	$b = R::dispense('country');
	foreach($data as $k=>$v)
		$b->$k = $v;
	foreach($allCols as $k)
		if(!in_array($k,$missingCols)&&(!isset($data[$k])||!$data[$k]))
			$missingCols[] = $k;
	R::store($b);
    unset($b);
}
$completesCols = array_diff($allCols,$missingCols);
print_r($allCols);
print_r($completesCols);
print_r($missingCols);