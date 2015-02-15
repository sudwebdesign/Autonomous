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
use Core\Session;
use Core\Post;
use Core\Filter;
use Core\Uploader;
use Tool\Geocoding;
use Model\Exception_Validation;
use Core\Dev;
class Modifier extends Presenter{
	function assign(){
		parent::assign();
		$this->taxonomy = lcfirst(end($this->presentNamespaces));
	}
	function dynamic(){
		parent::dynamic();
		Session::start(); //session auto start when get a key, if output not bufferised but direct flushed, have to start first
		$uri = $this->URI;
		if(!filter_var($uri[2],FILTER_VALIDATE_INT)){
			$q = new Query($this->taxonomy);
			if(filter_var($uri[1],FILTER_VALIDATE_INT)&&($redirect = $q->select('titleHref')->where('id=?',[$uri[1]])->getCell()))
				$this->redirect($redirect,$uri[1]);
			elseif($redirect = $q->select('id')->where('"titleHref"=?',[$uri[2]])->getCell())
				$this->redirect($redirect);
			exit;
		}

		$this->POST($uri[2]);
		$this->Query = (new Query($this->taxonomy))
			->where('"'.$this->taxonomy.'"'.'.id=?',[$uri[2]])
		;
		
#row4D soluce 4D rework
/*
$leTablEau = $this->Query->row();
$this->item = $leTablEau[0];
$user = R::findOne('user','id='.$this->item->user_id);
$this->item->user = $user;
#var_dump('<h1>$this->Query->row4D() sol </h1>',$this->item->titleHref,$this->item->id,$this->item->user->email);exit;
*/
	#	$this->item = $this->Query->row();#rowObject()row()::yep but is protected #rowRw()::toolargeinfo!no::getTable()
	#	$this->item = $this->item;
//		$this->item = $this->Query->row4D();
#		$this->item = R::findAll($this->taxonomy,' LIMIT '.$this->limitation.' OFFSET '.$this->offset);
		
		$this->item = $this->Query->row4D();
		#var_dump($this->item->tag,$this->item->presentationHtml);exit;
		
		
		if(!$this->item->titleHref)
			$this->item->titleHref = $this->URI->filterParam($this->item->title);
		if($uri[1]!=$this->item->titleHref)
			$this->redirect($this->item->titleHref);
		$this->img = $this->imageByItem();
		$this->files = $this->filesByItem();
		$this->item->atitle = htmlspecialchars($this->item->title, ENT_COMPAT);
                $this->item->presentation = $this->item->presentationHtml;
	}
	function imageByItem($item=null){
		if(!isset($item))
			$item = $this->item;
		return 'content/'.$this->taxonomy.'/'.$item->id.'/'.$this->URI->filterParam($item->title).'.png';
	}
	function filesByItem(){
		if(!isset($item))
			$item = $this->item;
		$files = glob('content/'.$this->taxonomy.'/'.$item->id.'/*', GLOB_BRACE);#var_dump($files);#exit;
		if(($i=array_search($this->imageByItem($item),$files))!==false){
			#var_dump($files[$i]);
			unset($files[$i]);
		}
		#var_dump($files);exit;
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
		if(!Dev::has(Dev::ROUTE))
			header('Location: '.$redirect,true,301);
		else
			echo 'Location: '.$redirect;
		exit;
	}	
/*#adupdate*/
	function POST($id){#var_dump(Post::getObject());exit;
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$type = $this->taxonomy;
		try{
			$entry = $this->POST_Common($type,$id);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($entry);
			//var_dump($entry,$this,$bean->xownDate);
                        R::store($entry);
			Post::clearPersistance();
		}
		catch(Exception_Validation $e){
			$this->formErrors = $e->getFlattenData();
			$this->formPosted = false;
		}
	}
	function POST_Common($type,$id){
		$entry = R::findOne($type,'id='.$id);//R::updateRecord($type);#create
		$this->maxFileSize = Uploader::file_upload_max_size();#var_dump($this->URI->filterParam($entry->title),$entry->title,$maxFileSize);exit;
		$that =& $this; // Assign by reference here!
		$entry->on('updated',function($entry)use($type,$that){
			$maxFileSize = $that->maxFileSize;#var_dump($maxFileSize);
			Uploader::files('content/'.$type.'/'.$entry->id.'/','files',null,$maxFileSize);
			Uploader::image(array(
				'dir'=>'content/'.$type.'/'.$entry->id.'/',
				'key'=>'image',
				'width'=>'90',
				'height'=>'90',
				//'rename'=>true, //image by default
				'rename'=>$entry->titleHref,#$this->URI->filterParam($entry->title),
				'extensions'=>array('png','jpg'),
				'conversion'=>'png',
				'maxFileSize'=>$maxFileSize
			));
		});
/*ForLocalDeBug*/#var_export($entry->user_id);
		$user = Session::get('email');#$user='ciit@free.fr';
		if($user&&$entry->user_id){#FLDB($user){#FOOL
			$user = R::findOne('user','id='.$entry->user_id);#FoulDawaComeÇa? pitetre
#			$user = R::findOne('user',['email'=>$user]);
			$entry->user = $user;
		}
		else
			$entry->error('user','required',true);
#		$user = R::findOne('user','id=1');#FLDB
#var_dump($entry->user->email,$entry->user->id);exit;
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
