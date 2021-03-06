<?php namespace service;
use Surikat\Tool\Geocoder\RadiusFinder;
use Surikat\Tool\CsvImporter;
use Surikat\Model\R;
class ServiceSetup {
	static function method(){
		set_time_limit(0);
		ob_implicit_flush(true);
		ob_end_flush();

		R::nuke();

		R::setUniqCheck(false);

		R::dropTable('taxonomy');
		$taxonomyTree = array(
			'Événement'=>array(
				'Salon',
				'Marché',
				'Vente-directe',
				'Chantier-collectif',
				'Spectacle',
				'Animation',
				'Conférence',
				'Projection',
				'Débat',
				'Fête',
				'Actu',
			),
			'Ressource'=>array(
				'Compétence',
				'Bénévolat',
				'Lieu',
				'Terrain',
				'Salle',
				'Outillage',
				'Véhicule',
			),
			'Projet'=>array(
				
			),
			'Annonce'=>array(
				'Loisirs',
				'Alimentation',
				'Santé',
				'Logement',
				'Education',
				'Energie',
				'Transport',
				'Vie-Pratique',
				'Art-et-culture',
			),
			'Association'=>array(),
			'Médiathèque'=>array(),
		);
		foreach($taxonomyTree as $name=>$v){
			$b = R::create('taxonomy',$name);
			foreach($v as $name2)
				$b->sharedTaxonomy[] = R::create('taxonomy',$name2);
			R::store($b);
		}

		R::dropTable('geoname');
		
		$geoname = R::getWriter()->prefix.'geoname';
		R::execMulti(<<<SQL
			CREATE TABLE "$geoname" (id SERIAL PRIMARY KEY);
			ALTER TABLE "$geoname" ADD "geonameid" bigint;
			ALTER TABLE "$geoname" ADD "name" text;
			ALTER TABLE "$geoname" ADD "asciiname" text;
			ALTER TABLE "$geoname" ADD "geoaltnames" text;
			ALTER TABLE "$geoname" ADD "latitude" double precision;
			ALTER TABLE "$geoname" ADD "longitude" double precision;
			ALTER TABLE "$geoname" ADD "fclass" text;
			ALTER TABLE "$geoname" ADD "fcode" text;
			ALTER TABLE "$geoname" ADD "country" text;
			ALTER TABLE "$geoname" ADD "cc2" text;
			ALTER TABLE "$geoname" ADD "admin1" text;
			ALTER TABLE "$geoname" ADD "admin2" text;
			ALTER TABLE "$geoname" ADD "admin3" text;
			ALTER TABLE "$geoname" ADD "admin4" text;
			ALTER TABLE "$geoname" ADD "population" integer;
			ALTER TABLE "$geoname" ADD "elevation" text;
			ALTER TABLE "$geoname" ADD "gtopo30" integer;
			ALTER TABLE "$geoname" ADD "timezone" text;
			ALTER TABLE "$geoname" ADD "moddate" date;
			ALTER TABLE "$geoname" ADD "radius" double precision;
			ALTER TABLE "$geoname" ADD "point" point;
SQL
);
		//R::execFile('sql/geoname-with-radius.sql');
		R::execMulti(str_replace('"geoname"','"'.$geoname.'"',file_get_contents('sql/geoname-with-radius.sql')));
		R::execFile('sql/function.geodistance.pgsql');

		/*
		CsvImporter::importation('geoname',
			array(
				'geonameid', //         : integer id of record in geonames database
				'name', //              : name of geographical point (utf8) varchar(200)
				'asciiname', //         : name of geographical point in plain ascii characters, varchar(200)
				'geoaltnames', //       : alternatenames, comma separated, ascii names automatically transliterated, convenience attribute from alternatename table, varchar(8000)
				'latitude', //          : latitude in decimal degrees (wgs84)
				'longitude', //         : longitude in decimal degrees (wgs84)
				'fclass', //            : see http://www.geonames.org/export/codes.html, char(1)
				'fcode', //             : see http://www.geonames.org/export/codes.html, varchar(10)
				'country', //           : ISO-3166 2-letter country code, 2 characters
				'cc2', //               : alternate country codes, comma separated, ISO-3166 2-letter country code, 60 characters
				'admin1', //            : fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)
				'admin2', //            : code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80)
				'admin3', //            : code for third level administrative division, varchar(20)
				'admin4', //            : code for fourth level administrative division, varchar(20)
				'population', //        : bigint (8 byte int)
				'elevation', //         : in meters, integer
				'gtopo30', //           : digital elevation Model, srtm3 or gtopo30, average elevation of 3''x3'' (ca 90mx90m) or 30''x30'' (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.
				'timezone', //          : the timezone id (see file timeZone.txt) varchar(40)
				'moddate', //           : date of last modification in yyyy-MM-dd format
			),
			null,
			array(
				'debug'=>3,
				'callback'=>function(&$data,&$continue){
					if($data['fcode']=='ADM4'){
						$data['asciiname'] = strtolower($data['asciiname']);
						$data['population'] = $data['population']?(int)$data['population']:null;
						$data['radius'] = RadiusFinder::byAddress($data['name'].', '.$data['country'],$data['latitude'],$data['longitude'],true);
					}
					else
						$continue = true;
				}
			)
		);
		*/
	}
}