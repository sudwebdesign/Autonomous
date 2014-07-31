<?php namespace present;
use uri;
use view;
use model;
use model\Query;
use model\Control_Geocoding;
use model\R;
use control;
use control\str;
use control\FS;
use control\PHP;
use control\session;
use control\post;
use control\filter;
use control\uploader;
use model\Exception_Validation;
class ajouter extends \present{
	function assign(){
		parent::assign();
		$this->action = uri::param(0);
		$this->taxonomy = end($this->presentNamespaces);
	}
	function dynamic(){
		parent::dynamic();
		session::start(); //session auto start when get a key, if output not bufferised but direct flushed, have to start first
		$this->POST();
	}
	function POST(){
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$type = $this->taxonomy;
		R::begin();
		try{
			$bean = $this->POST_Common($type);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($bean);
			//exit(print('<pre>'.print_r($bean->getArray(),true)));
			R::store($bean);
			if($e=$bean->getErrors())
				throw new Exception_Validation('Données manquantes ou erronées',$e);
			R::commit();
			post::clearPersistance();
		}
		catch(Exception_Validation $e){
			R::rollback();
			$this->formErrors = $e->getData();
			$this->formPosted = false;
		}
	}
	function POST_Common($type){
		$bean = R::create($type);
		$bean->on('created',function($bean)use($type){
			uploader::image(array(
				'dir'=>'content/'.$type.'/'.$bean->id.'/',
				'key'=>'image',
				'width'=>'90',
				'height'=>'90',
				//'rename'=>true, //image by default
				'rename'=>uri::filterParam($bean->title),
				'extensions'=>array('png','jpg'),
				'conversion'=>'png'
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
			
		if(isset($_POST['title']))
			$bean->title = strip_tags($_POST['title']);
		if(isset($_POST['tel']))
			$bean->tel = $_POST['tel'];
		if(isset($_POST['url']))
			$bean->url = filter::url($_POST['url']);
			
		if(isset($_POST['presentation']))
			$bean->presentation = filter::strip_tags_basic($_POST['presentation']);
		if(isset($_POST['sharedTag'])&&is_array($_POST['sharedTag'])&&isset($_POST['sharedTag']['label'])&&trim($_POST['sharedTag']['label'])){
			$max = 5;
			$tags = explode(' ',strip_tags($_POST['sharedTag']['label']));
			$taxonomyO = R::load('taxonomy',$this->taxonomy);
			if(!$taxonomyO)
				throw new \Exception(sprintf("Error: Taxonomy %s not found",$this->taxonomy));
			foreach($tags as $i=>$tag){
				if($i>=$max)
					break;
				$tag = uri::filterParam($tag);
				if(empty($tag))
					continue;
				if($t=R::load('taxonomy',$tag)){
					if(isset($user))
						$t->sharedUser[] = $user;
					$bean->sharedTaxonomy[] = $t;
				}
				elseif($t=R::load('tag',$tag)){
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
		if(isset($_POST['xownGeopoint'])&&is_array($_POST['xownGeopoint']))
			Control_Geocoding::POST_Geo($bean,$_POST['xownGeopoint']);
		return $bean;
	}
}