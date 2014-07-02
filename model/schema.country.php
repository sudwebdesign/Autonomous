<?php namespace model;
use control;
use control\CsvIterator;
$d = control::$CWD.'/.data/';
$countries = new CsvIterator($d.'country.csv',';');
$countries->setKeys(array(
	0 =>'label',
	3 =>'nameEn',
	7 =>'nameFr',
	9 =>'nameEs',
	25 =>'nameIt',
	26 =>'nameDe',
	13 =>'nameRu',
	4 =>'nameEnReading',
	8 =>'nameFrReading',
	14 =>'nameRuReading',
	15=>true,
	16=>true,
	20=>true,
	21=>true,
));

//$langs = new CsvIterator($d.'langs.csv',';');
//$lg = array();
//foreach($langs as $i=>$data){
	//if(trim($data[0]))
		//$lg[] = array($data[0],ucfirst($data[2]),$data[3]);
//}

$emptyCols = array();
$missingCols = array();
$completesCols = array();
$allCols = array();
foreach($countries as $i=>$data){
	//exit(print_r($data));
	//$b = R::dispense('country'),
	$b = $data;
	unset($b[15]);
	unset($b[16]);
	unset($b[20]);
	unset($b[21]);
	$b = (object)$b;
	if($data[15])
		$ik = 15;
	else
		$ik = 20;
	if(strpos($data[$ik],'/')!==false){
		$x = explode('/',$data[$ik]);
		foreach($x as $v){
			$_x = explode('(',rtrim($v,')'));
			$name = $_x[0];
			$lk = isset($_x[1])?$_x[1]:null;
			$k = 'name'.(isset($lk)?ucfirst($lk):'');
			$b->$k = $name;
		}
		$x = explode('/',$data[$ik+1]);
		foreach($x as $v){
			$_x = explode('(',rtrim($v,')'));
			$name = $_x[0];
			$lk = isset($_x[1])?$_x[1]:null;
			$k = 'name'.(isset($lk)?ucfirst($lk):'');
			$b->$k = $name;
		}
	}
	else{
		$b->name = $data[$ik];
		$b->nameReading = $data[$ik+1];
	}
	foreach($b as $k=>$v){
		if(!$v){
			if(!in_array($k,$emptyCols))
				$emptyCols[] = $k;
		}
		elseif(!in_array($k,$completesCols))
			$completesCols[] = $k;
		if(!in_array($k,$allCols))
			$allCols[] = $k;
	}
	//print($i);
	print_r($b);
    unset($b);
}
print_r($allCols);
print_r($completesCols);
print_r($emptyCols);
print_r($missingCols);

/*
	0 =>'ISO 3166-1 alpha-2',
	1 =>'ISO 3166-1 alpha-3',
	2 =>'ISO 3166-1 numeric',
	3 =>'ISO 3166-1 English short name (Gazetteer order)',
	4 =>'ISO 3166-1 English short name (proper reading order)',
	5 =>'ISO 3166-1 English romanized short name (Gazetteer order)',
	6 =>'ISO 3166-1 English romanized short name (proper reading oorder)',
	7 =>'ISO 3166-1 French short name (Gazetteer order)',
	8 =>'ISO 3166-1 French short name (proper reading order)',
	9 =>'ISO 3166-1 Spanish short name (Gazetteer order)',
	10 =>'UNGEGN English formal name',
	11 =>'UNGEGN French formal name',
	12 =>'UNGEGN Spanish formal name',
	13 =>'UNGEGN Russian short name',
	14 =>'UNGEGN Russian formal name',
	15 =>'UNGEGN local short name',
	16 =>'UNGEGN local formal name',
	17 =>'BGN English short name (Gazetteer order)',
	18 =>'BGN English short name (proper reading order)',
	19 =>'BGN English long name',
	20 =>'BGN local short name',
	21 =>'BGN local long name',
	22 =>'PCGN English short name (Gazetteer order)',
	23 =>'PCGN English short name (proper reading order)',
	24 =>'PCGN English long name',
	25 =>'FAO Italian long name',
	26 =>'FFO German short name',
*/