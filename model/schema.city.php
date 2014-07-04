<?php namespace model;
use control;
use control\CsvImporter;
//https://github.com/petewarden/dstkdata/blob/master/worldcitiespop.csv and rename it in .data/city.csv without comments and cols first line, just data
CsvImporter::importation('city',
	array(
		'country',
		'label',
		'labelFind',
		'lat',
		'lng',
		'population',
	),
	array(
		0,
		2,
		1,
		5,
		6,
		4,
	),
	array(
		'debug'=>3,
		'separator'=>',',
		'utf8_encode'=>true,
		'callback'=>function(&$data,&$continue){
			if(!$data['label'])
				$continue = true;
			$data['label'] = (string)$data['label'];
			$data['lat'] = (float)$data['lat'];
			$data['lng'] = (float)$data['lng'];
			$data['population'] = $data['population']?(int)$data['population']:null;
		}
	)
);