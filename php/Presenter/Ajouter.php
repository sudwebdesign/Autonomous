<?php namespace Presenter;
use Uri;
use View;
use Model;
use Model\Query;
use Model\Control_Geocoding;
use Model\R;
use Tool\str;
use Tool\FS;
use Tool\PHP;
use Core\Session;
use Core\Post;
use Core\Filter;
use Core\Uploader;
use Model\Exception_Validation;
class Ajouter extends Basic{
	function assign(){
		parent::assign();
		$this->action = $this->URI[0];
		$this->taxonomy = lcfirst(end($this->presentNamespaces));
	}
	function dynamic(){
		parent::dynamic();
		Session::start(); //session auto start when get a key, if output not bufferised but direct flushed, have to start first
		$this->POST();
	}
	function POST(){
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$type = $this->taxonomy;
		try{
			$entry = $this->POST_Common($type);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($entry);
			$entry->store();
			Post::clearPersistance();
		}
		catch(Exception_Validation $e){
			$this->formErrors = $e->getFlattenData();
			$this->formPosted = false;
		}
	}
	function POST_Common($type){
		$entry = R::create($type);
		$entry->on('created',function($entry)use($type){
			Uploader::image(array(
				'dir'=>'content/'.$type.'/'.$entry->id.'/',
				'key'=>'image',
				'width'=>'90',
				'height'=>'90',
				//'rename'=>true, //image by default
				'rename'=>$this->URI->filterParam($entry->title),
				'extensions'=>array('png','jpg'),
				'conversion'=>'png'
			));
			Uploader::files('content/'.$type.'/'.$entry->id.'/','files');
		});

		$user = Session::get('email');
		if($user){
			$user = R::findOrNewOne('user',array('email'=>$user));
			$entry->user = $user;
		}
		else
			$entry->error('user','required',true);
		$P = Post::getObject();
		$entry->title = strip_tags($P->title);
		$entry->tel = $P->tel;
		$entry->url = Filter::url($P->url);
		$entry->presentation = Filter::strip_tags_basic($P->presentation);
		if(is_object($P->sharedTag)&&trim($P->sharedTag->name)){
			$max = 5;
			$tags = explode(' ',strip_tags($P->sharedTag->name));
			$taxonomy = R::load('taxonomy',$this->presentAttributes->TAXONOMY);
			foreach($tags as $i=>$t){
				if($i>=$max)
					break;
				$t = $this->URI->filterParam($t);
				if(empty($t))
					continue;
				$tag = R::findOrNewOne('tag',$t);
				$tag->sharedUser[] = $entry->user;
				$tag->sharedTaxonomy[] = $taxonomy;
				$entry->sharedTag[] = $tag;
			}
		}
		if(is_object($G=$P->ownGeopoint)&&$G->label&&$G->lat!=''&&$G->lon!=''){
			$entry->xownGeopoint[] = R::create('geopoint',[
				'label' => $G->label,
				'lat' => $G->lat,
				'lon' => $G->lon,
				'radius' => $G->radius,
			]);
		}
		return $entry;
	}
}