<?php namespace model;
use control;
use control\CsvImporter;
CsvImporter::importation('lang',
	array(
		'label',
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
			static $labels = array();
			if(!$data['label']||in_array($data['label'],$labels)){
				$continue = true;
				return;
			}
			$labels[] = $data['label'];
			$data['nameEn'] = ucfirst(@$data['nameEn']);
			$data['nameFr'] = ucfirst(@$data['nameFr']);
		}
	)
);