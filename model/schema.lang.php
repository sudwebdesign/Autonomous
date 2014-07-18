<?php namespace model;
use control;
use control\CsvImporter;
CsvImporter::importation('lang',
	array(
		'name',
		'nameEn',
		'nameFr',
	),
	array(
		0,
		2,
		3,
	),
	array(
		'utf8_encode'=>true,
		//'debug'=>1,
		'callback'=>function(&$data,&$continue){
			static $names = array();
			if(!$data['name']||in_array($data['name'],$names)){
				$continue = true;
				return;
			}
			$names[] = $data['name'];
			$data['nameEn'] = ucfirst(@$data['nameEn']);
			$data['nameFr'] = ucfirst(@$data['nameFr']);
		}
	)
);