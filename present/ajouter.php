<?php namespace present;
use view;
use model;
use model\R;
use surikat\control\Uploader;
use surikat\model\Exception_Validation;
class ajouter extends \present{
	static function compileVars(&$vars=array()){
		$vars['action'] = view::param(0);
	}
	static function compileElement(){
		
	}
	static function exec(){
	}
	protected static function commonCreate($type){
		$bean = R::newOne($type,array(
			'created'=>date('Y-m-d H:i:s',time()),
		));
		//$bean->onCreated(function($bean,$id)use($type){
			//Uploader::image('content/'.$type.'/'.$id.'/','image','90','90')
		//});
		if(isset($_SESSION['email'])&&($user=R::findOne('users','email=?',array($_SESSION['email']))))
			$bean->user = $user;
		else
			$bean->error('user','required');
		if(isset($_POST['label']))
			$bean->label = $_POST['label'];
		if(isset($_POST['presentation']))
			$bean->presentation = $_POST['presentation'];
		if(isset($_POST['tags'])){
			$tags = explode(',',$_POST['tags']);
			foreach($tags as $tag)
				if(!empty($tag))
					$bean->sharedTags[] = R::findOrNewOne(array('taxonomy','tags'),array('label'=>$tag));
		}
		if(isset($_POST['geo'])){
			$geo = $_POST['geo'];
			$valid = @$_POST['geo-valid']==='true';
			$label = null;
			$locality = null;
			if($valid){
				if(($js=json_decode(file_get_contents(sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&region=%s&sensor=false',urlencode($geo),model::DEFAULT_COUNTRY_CODE))))&&$js->status==='OK'&&isset($js->results[0])&&($result=$js->results[0])){
					$political = array();
					foreach($result->address_components as $compo){
						if(in_array('political',$compo->types))
							$political[] = $compo->long_name;
						switch($compo->types[0]){
							case 'locality':
								$locality = R::findOrNewOne('locality',array('label'=>$compo->long_name));
							break;
							case 'administrative_area_level_2':
								if((int)$compo->short_name!=model::DEFAULT_DEPARTEMENT_CODE)
									$bean->error('geo','administrative_area_level_2');
							break;
							case 'country':
								if($compo->short_name!=strtoupper(model::DEFAULT_COUNTRY_CODE))
									$bean->error('geo','country');
							break;
						}
					}
					$x = explode(',',$geo);
					if(count($x)>2){
						array_pop($x);
						array_pop($x);
						foreach($x as $_x)
							if(!in_array($_x=trim($_x),$political))
								$label .= $_x.' ';
						$label = rtrim($label);
					}
				}
				else{
					$bean->error('geo','not_found');
				}
			}
			else{
				$label = htmlentities($geo);
			}
			$lat = (float)$_POST['lat'];
			$lng = (float)$_POST['lng'];
			$latLngOk = $lat&&$lng&&$lat<=90.0&&$lat>=-90.0&&$lng<=180.0&&$lng>=-180.0;
			if($latLngOk||$label||$locality){
				$geopoint = R::newOne('geopoints',array(
					'label'=>$label,
					'lat'=>$latLngOk?(float)$_POST['lat']:null,
					'lng'=>$latLngOk?(float)$_POST['lng']:null,
					'rayon'=>$latLngOk&&isset($_POST['rayon'])?(float)$_POST['rayon']:null,
				));
				if($locality)
					$geopoint->locality = $locality;
				$bean->sharedGeopoints[] = $geopoint;
			}
			
		}
		return $bean;
	}
	protected static function POST($type){
		if(empty($_POST))
			return;
		self::variable('formPosted',true);
		try{
			static::POST(self::commonCreate($type));
		}
		catch(Exception_Validation $e){
			self::variable(array(
				'formErrors'=>$e->getData(),
				'formPosted'=>false
			));
		}
	}
	
	static function execVars(&$vars=array()){
		
	}
}
