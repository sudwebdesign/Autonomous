<?php namespace model;
use control;
use control\CsvImporter;
CsvImporter::importation('country',
	array(
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
	),
	array(
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
		
	),
	array(
		//'utf8_encode'=>false,
		//'debug'=>1,
	)
);