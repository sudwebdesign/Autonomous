<?php namespace model;
use control;
use control\CsvImporter;
//https://github.com/petewarden/dstkdata/blob/master/worldcitiespop.csv and rename it in .data/city.csv without comments and cols first line, just data
CsvImporter::importation('geoname',
	array(
		'geonameid', //         : integer id of record in geonames database
		'name', //              : name of geographical point (utf8) varchar(200)
		'nameFind', //          : name of geographical point in plain ascii characters, varchar(200)
		'xownGeoalt', //          : alternatenames, comma separated, ascii names automatically transliterated, convenience attribute from alternatename table, varchar(8000)
		'lat', //               : latitude in decimal degrees (wgs84)
		'lng', //               : longitude in decimal degrees (wgs84)
		'featureClass', //      : see http://www.geonames.org/export/codes.html, char(1)
		'featureCode', //       : see http://www.geonames.org/export/codes.html, varchar(10)
		'countryCode', //       : ISO-3166 2-letter country code, 2 characters
		'countryCode2', //      : alternate country codes, comma separated, ISO-3166 2-letter country code, 60 characters
		'areaLevel1', //        : fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)
		'areaLevel2', //        : code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80) 
		'areaLevel3', //        : code for third level administrative division, varchar(20)
		'areaLevel4', //        : code for fourth level administrative division, varchar(20)
		'population', //        : bigint (8 byte int) 
		'elevation', //         : in meters, integer
		'dem', //               : digital elevation model, srtm3 or gtopo30, average elevation of 3''x3'' (ca 90mx90m) or 30''x30'' (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.
		'timezone', //          : the timezone id (see file timeZone.txt) varchar(40)
		'modifiedDate', // : date of last modification in yyyy-MM-dd format
	),
	null,
	array(
		'debug'=>3,
		'separator'=>"\t",
		'callback'=>function(&$data,&$continue){
			static $uns = array();
			$data['un'] = (string)$data['name'];
			if(in_array($data['un'],$uns))
				$data['un'] .= '-'.$data['geonameid'];
			$uns[] = $data['name'];
			$data['name'] = (string)$data['name'];
			$data['nameFind'] = strtolower($data['nameFind']);

			$x = explode(',',$data['xownGeoalt']);
			$data['xownGeoalt'] = array();
			foreach($x as $v)
				if(trim($v))
					$data['xownGeoalt'][] = R::newOne('geoalt',array('name'=>$v));
			
			$data['lat'] = (float)$data['lat'];
			$data['lng'] = (float)$data['lng'];
			$data['population'] = $data['population']?(int)$data['population']:null;
		}
	)
);