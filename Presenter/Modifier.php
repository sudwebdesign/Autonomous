<?php namespace Presenter;
use Uri;
use View;
use Model;
use Model\Query;
use Model\R;
use Tool;
use Tool\str;
use Tool\FS;
use Tool\PHP;
use Tool\session;
use Tool\post;
use Tool\filter;
use Tool\uploader;
use Tool\Geocoding;
use Model\Exception_Validation;
use Dev;
use Tool\ArrayObject;
class Modifier extends Basic{
	function assign(){
		parent::assign();
		$this->taxonomy = lcfirst(end($this->presentNamespaces));
#		var_export($this);exit;
	}
	function dynamic(){
		parent::dynamic();
		session::start(); //session auto start when get a key, if output not bufferised but direct flushed, have to start first
		$uri = $this->URI;
		$this->POST($uri[2]);
		if(!filter_var($uri[2],FILTER_VALIDATE_INT)){
			$q = Query::getNew($this->taxonomy);
			if(filter_var($uri[1],FILTER_VALIDATE_INT)&&($redirect = $q->select('titleHref')->where('id=?',[$uri[1]])->getCell()))
				$this->redirect($redirect,$uri[1]);
			elseif($redirect = $q->select('id')->where('"titleHref"=?',[$uri[1]])->getCell())
				$this->redirect($redirect);
			exit;
		}
		$this->Query = Query::getNew($this->taxonomy)
			->where($this->taxonomy.'.id=?',[$uri[2]])
		;
		$this->item = $this->Query->row4D();
		if(!$this->item->titleHref)
			$this->item->titleHref = uri::filterParam($this->item->title);
		if($uri[1]!=$this->item->titleHref)
			$this->redirect($this->item->titleHref);
		$this->img = $this->imageByItem();
		$this->files = $this->filesByItem();
		$this->item->atitle = htmlspecialchars($this->item->title, ENT_COMPAT);
	}
	function imageByItem($item=null){
		if(!isset($item))
			$item = $this->item;
		return '/content/'.$this->taxonomy.'/'.$item->id.'/'.uri::filterParam($item->title).'.png';
	}
	function filesByItem(){
		if(!isset($item))
			$item = $this->item;
		$files = glob('content/'.$this->taxonomy.'/'.$item->id.'/*', GLOB_BRACE);
		if(($i=array_search($this->imageByItem($item),$files))!==false)
			unset($files[$i]);
		return $files;
	}
	function redirect($location=null,$location2=null){
		$title = $this->URI[1];
		$id = $this->URI[2];
		if(isset($location)){
			if(filter_var($location,FILTER_VALIDATE_INT))
				$id = $location;
			else
				$title = $location;
		}
		if(isset($location2)){
			if(filter_var($location2,FILTER_VALIDATE_INT))
				$id = $location2;
			else
				$title = $location2;
		}
		$redirect = $this->URI[0].'+'.$title.'+'.$id;
		if(!dev::has(dev::URI))
			header('Location: '.$redirect,true,301);
		else
			echo 'Location: '.$redirect;
		exit;
	}	
/*#adupdate*/
	function POST($id){
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$type = $this->taxonomy;#var_export($id);exit;
		try{
			$entry = $this->POST_Common($type,$id);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($entry);
			R::store($entry);
			post::clearPersistance();
		}
		catch(Exception_Validation $e){
			$this->formErrors = $e->getData();
			$this->formPosted = false;
		}
	}
	function POST_Common($type,$id){
		//	public function updateRecord( $type, $updatevalues, $id = NULL );
		$entry = R::findOne($type,'id='.$id);//R::updateRecord($type);#create
		$entry->on('updated',function($entry)use($type){
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
/*ForLocalDeBug*/#var_export($entry->user_id);exit;
		$user = session::get('email');
		if($user&&$entry->user_id){#FLDB($user){#FOOL
			$user = R::findOne('user','id='.$entry->user_id);#FLDB
#			$user = R::findOne('user',array('email'=>$user));#FOOL@ire
			$entry->user = $user;
		}
		else
			$entry->error('user','required',true);
#		$user = R::findOne('user','id=1');#FLDB
		$P = post::getObject();
		$entry->title = strip_tags($P->title);
		$entry->tel = $P->tel;
		$entry->url = filter::url($P->url);
		$entry->presentation = filter::strip_tags_basic($P->presentation);
		if(is_object($P->sharedTag)&&trim($P->sharedTag->name)){
			$max = 5;
			$tags = explode(' ',strip_tags($P->sharedTag->name));
			$taxonomy = R::load('taxonomy',$this->presentAttributes->TAXONOMY);
			foreach($tags as $i=>$t){
				if($i>=$max)
					break;
				$t = uri::filterParam($t);
				if(empty($t))
					continue;
				$tag = R::findOrNewOne('tag',$t);
				$tag->sharedUser[] = $entry->user;
				$tag->sharedTaxonomy[] = $taxonomy;
				$entry->sharedTag[] = $tag;
			}
		}
		if(is_object($G=$P->xownGeopoint)&&$G->label&&$G->lat!=''&&$G->lon!=''){
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
