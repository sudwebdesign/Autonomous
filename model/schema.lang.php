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
			if(!$data['label'])
				$continue = true;
		}
	)
);