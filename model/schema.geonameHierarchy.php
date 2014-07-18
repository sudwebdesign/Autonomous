<?php namespace model;
use control;
use control\CsvIterator;
$csvIterator = new CsvIterator(control::$CWD.'.data/geonameHierarchy.csv',"\t");
foreach($csvIterator as $i=>$line){
	var_dump($line);exit;
}