<?php namespace model;
class Control_Geocoding{
	static function POST_Geo($bean,$geop){
		$geopoint = R::newOne('geopoint',array(
			'lat'=>@$geop['lat'],
			'lng'=>@$geop['lng'],
			'rayon'=>@$geop['rayon'],
		));
		if(@$geop['valid']==='true'&&@$geop['label']){
			$file = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&region=%s&sensor=false',urlencode($geop['label']),model::DEFAULT_COUNTRY_CODE);
			$tmpDir = control::$TMP.'geocode_cache/address/';
			if(is_file($tmpFile=$tmpDir.sha1($file)))
				$content = file_get_contents($tmpFile);
			else{
				$content = file_get_contents($file);
				FS::mkdir($tmpFile,true);
				file_put_contents($tmpFile,$content);
			}
			if(($js=json_decode($content))&&$js->status==='OK'&&isset($js->results[0])&&($result=$js->results[0])){
				$political = array();
				foreach($result->address_components as $compo){
					if(in_array('political',$compo->types))
						$political[] = $compo->long_name;
					switch($compo->types[0]){
						case 'locality':
							$bean->sharedLocality[] = $geopoint->locality = R::findOrNewOne('locality',array('label'=>$compo->long_name));
						break;
						case 'administrative_area_level_1':
							$geoarealevel1 = R::findOrNewOne('geoarealevel1',
								array('code'=>$compo->short_name),
								array('label'=>$compo->long_name)
							);
							$geopoint->geoarealevel1 = $geoarealevel1;
							if($geopoint->locality)
								$geopoint->locality->geoarealevel1 = $geoarealevel1;
						break;
						case 'administrative_area_level_2':
							if((int)$compo->short_name!=model::DEFAULT_DEPARTEMENT_CODE)
								$bean->error('xownGeopoint.label','administrative_area_level_2');
							$geoarealevel2 = R::findOrNewOne('geoarealevel2',
								array('code'=>$compo->short_name),
								array('label'=>$compo->long_name)
							);
							$geopoint->geoarealevel2 = $geoarealevel2;
							if($geopoint->locality)
								$geopoint->locality->geoarealevel2 = $geoarealevel2;
							if($geopoint->geoarealevel1)
								$geoarealevel2->geoarealevel1 = $geopoint->geoarealevel1;
						break;
						case 'country':
							if($compo->short_name!=strtoupper(model::DEFAULT_COUNTRY_CODE))
								$bean->error('xownGeopoint.label','geocountry');
							$country = R::findOrNewOne('geocountry',
								array('code'=>$compo->short_name),
								array('label'=>$compo->long_name)
							);
							$geopoint->geocountry = $country;
							if($geopoint->locality)
								$geopoint->locality->geocountry = $country;
							if($geopoint->geoarealevel2)
								$geopoint->geoarealevel2->geocountry = $country;
						break;
					}
				}
				$x = explode(',',$geop['label']);
				if(count($x)>2){
					array_pop($x);
					array_pop($x);
					$label = '';
					foreach($x as $_x)
						if(!in_array($_x=trim($_x),$political))
							$label .= $_x.' ';
					$geopoint->label = rtrim($label);
				}
			}
			else
				$bean->error('xownGeopoint.label','not_found');
		}
		else
			$geopoint->label = htmlentities((string)@$geop['label']);
		if($geopoint->lat!=''||$geopoint->lng!=''||$geopoint->label||$geopoint->locality)
			$bean->xownGeopoint[] = $geopoint;
	}
}
