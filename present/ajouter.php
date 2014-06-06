<?php namespace present;
use view;
use model;
use control;
use surikat\model\R;
use surikat\control\FS;
use surikat\control\PHP;
use surikat\control\session;
use surikat\control\post;
use surikat\control\filter;
use surikat\control\uploader;
use surikat\model\Exception_Validation;
class ajouter extends \present{
	static function compileVars(&$vars=array()){
		$vars['action'] = view::param(0);
	}
	static function compileElement(){
		
	}
	static function exec(){
		session::start(); //session auto start when get a key, if output not bufferised but direct flushed, have to start first
		self::variable('taxonomy',end(self::$options['namespaces']));
		self::POST();
	}
	static function execVars(&$vars=array()){
		
	}
	
	protected static function POST(){
		if(!count(self::$options['namespaces'])>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		self::variable('formPosted',true);
		$type = self::variable('taxonomy');
		R::begin();
		try{
			$bean = self::POST_Common($type);
			if(method_exists(($final=self::$final),'POST_Specifications'))
				$final::POST_Specifications($bean);
			//exit(print('<pre>'.print_r($bean->getArray(),true)));
			R::store($bean);
			R::commit();
			post::clearPersistance();
		}
		catch(Exception_Validation $e){
			R::rollback();
			self::variable(array(
				'formErrors'=>$e->getData(),
				'formPosted'=>false
			));
		}
	}
	protected static function POST_Common($type){
		$bean = R::dispense($type);
		$bean->on('created',function($bean)use($type){
			uploader::image(array(
				'dir'=>'content/'.$type.'/'.$bean->id.'/',
				'key'=>'image',
				'width'=>'90',
				'height'=>'90',
				//'rename'=>true, //image by default
				'rename'=>$bean->titre,
			));
			uploader::files('content/'.$type.'/'.$bean->id.'/','files');
		});
		$email = session::get('email');
		if(!$email)
			$bean->error('user','required');
		else{
			$user = R::findOrNewOne('user',array('email'=>$email));
			$bean->user = $user;
		}
			
		if(isset($_POST['titre']))
			$bean->titre = $_POST['titre'];
		if(isset($_POST['tel']))
			$bean->tel = $_POST['tel'];
		if(isset($_POST['url']))
			$bean->url = filter::url($_POST['url']);
			
		if(isset($_POST['presentation']))
			$bean->presentation = filter::strip_tags_basic($_POST['presentation']);
		if(isset($_POST['tags'])&&trim($_POST['tags'])){
			$tags = explode(',',$_POST['tags']);
			$taxonomyO = model::load('taxonomy',self::variable('taxonomy'));

			//$joinTag = array($type,'tag');
			//$joinTaxonomy = array($type,'taxonomy');
			//sort($joinTag);
			//sort($joinTaxonomy);
			//$joinTag = implode('_',$joinTag);
			//$joinTaxonomy = implode('_',$joinTaxonomy);
			
			foreach($tags as $tag){
				$tag = trim($tag);
				if(empty($tag))
					continue;
				if($t=model::load('taxonomy',$tag)){
					if(isset($user))
						$t->sharedUser[] = $user;
					$bean->sharedTaxonomy[] = $t;
				}
				elseif($t=model::load('tag',$tag)){
					//R::getCell("SELECT id FROM $joinTag WHERE {$type}_id={} LIMIT 1",array());
					$t->sharedTaxonomy[] = $taxonomyO;
					if(isset($user))
						$t->sharedUser[] = $user;
					$bean->sharedTag[] = $t;
				}
				else{
					$t = R::newOne('tag',$tag);
					$t->sharedTaxonomy[] = $taxonomyO;
					if(isset($user))
						$t->sharedUser[] = $user;
					$bean->sharedTag[] = $t;
				}
			}
		}
		self::POST_Geo($bean);
		return $bean;
	}
	static function POST_Geo($bean){
		if(!isset($_POST['geo']))
			return;
		$geo = $_POST['geo'];
		$valid = @$_POST['geo-valid']==='true';
		$label = null;
		$locality = null;
		if($valid){
			$file = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&region=%s&sensor=false',urlencode($geo),model::DEFAULT_COUNTRY_CODE);

			$tmpDir = control::$TMP.'cache/.geocode-address/';
			if(is_file($tmpFile=$tmpDir.sha1($file))){
				$content = file_get_contents($tmpFile);
			}
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
			//R::debug();
			$geopoint = R::newOne('geopoint',array(
				'label'=>$label,
				'lat'=>$latLngOk?(float)$_POST['lat']:null,
				'lng'=>$latLngOk?(float)$_POST['lng']:null,
				'point'=>$latLngOk?'POINT('.$_POST['lat'].' '.$_POST['lng'].')':null,
				'rayon'=>$latLngOk&&isset($_POST['rayon'])?(float)$_POST['rayon']:null,
			));
			if($locality)
				$geopoint->locality = $locality;
			$bean->xownGeopoint[] = $geopoint;
		}
	}
}
