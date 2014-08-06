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
			$entry = $this->POST_Common($type);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($entry);
			//exit(print('<pre>'.print_r($entry->getArray(),true)));
			R::store($entry);
			if($e=$entry->getErrors())
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
		$entry = R::create($type);
		$entry->on('created',function($entry)use($type){
			uploader::image(array(
				'dir'=>'content/'.$type.'/'.$entry->id.'/',
				'key'=>'image',
				'width'=>'90',
				'height'=>'90',
				//'rename'=>true, //image by default
				'rename'=>uri::filterParam($entry->title),
				'extensions'=>array('png','jpg'),
				'conversion'=>'png'
			));
			uploader::files('content/'.$type.'/'.$entry->id.'/','files');
		});

		$user = session::get('email');
		if($user){
			$user = R::findOrNewOne('user',array('email'=>$user));
			$entry->user = $user;
		}
		else
			$entry->error('user','required');
			
		if(isset($_POST['title']))
			$entry->title = strip_tags($_POST['title']);
		if(isset($_POST['tel']))
			$entry->tel = $_POST['tel'];
		if(isset($_POST['url']))
			$entry->url = filter::url($_POST['url']);
			
		if(isset($_POST['presentation']))
			$entry->presentation = filter::strip_tags_basic($_POST['presentation']);

		if(isset($_POST['sharedTag'])&&is_array($_POST['sharedTag'])&&isset($_POST['sharedTag']['name'])&&trim($_POST['sharedTag']['name'])){
			$max = 5;
			$tags = explode(' ',strip_tags($_POST['sharedTag']['name']));
			foreach($tags as $i=>$t){
				if($i>=$max)
					break;
				$t = uri::filterParam($t);
				if(empty($t))
					continue;
				$tag = R::findOrNewOne('tag',$t);
				$tag->sharedUser[] = $entry->user;
				$entry->sharedTag[] = $tag;
			}
		}

		
			
		return $entry;
	}
}