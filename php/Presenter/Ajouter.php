<?php namespace Presenter;
use FileSystem\FS;
use FileSystem\Uploader;
use Model;
use Model\R;
use Model\Query;
use Model\Control_Geocoding;
use Model\AbstractMainTable;
use Model\Exception_Validation;
use SourceCode\PHP;
use Uri;
use User\Post;
use User\Session;
use Validation\Filter;
use Vars\STR;
class Ajouter extends Presenter{
	function assign(){
		parent::assign();
		$this->action = $this->URI[0];
		$this->taxonomy = lcfirst(end($this->presentNamespaces));
	}
	function dynamic(){
		parent::dynamic();
		$this->POST();
	}
	function POST(){#var_dump(Post::getObject());exit;
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$type = $this->taxonomy;
		try{
                        R::begin();
			$entry = $this->POST_Common($type);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($entry);
	#		var_dump('<h1>grpby bugy mais ça poste</h1',$entry,$entry->groupBy('user.email'));exit;#toobigexport4firefox
			$entry->store();
                        R::commit();
			Post::clearPersistance();
		}
		catch(Exception_Validation $e){
                        R::rollback();
			$this->formErrors = $e->getFlattenData();
			$this->formPosted = false;
		}
	}
	function POST_Common($type){
		$entry = R::create($type);#AbstractMainTable::
		$user = $this->userSessionEmail;
		if($user)
                    $entry->user = R::findOrNewOne('user',array('email'=>$user));
		else
                    $entry->error('user','required',true);


		$this->maxFileSize = Uploader::file_upload_max_size();#var_dump($this->URI->filterParam($entry->title),$entry->title,$maxFileSize);exit;
		$that =& $this; // Assign by reference here!
		$entry->on('created',function($entry)use($type,$that){
			$maxFileSize = $that->maxFileSize;#var_dump($maxFileSize);
			Uploader::files('content/'.$type.'/'.$entry->id.'/','files',null,null,$maxFileSize);
			Uploader::image(array(
				'dir'=>'content/'.$type.'/'.$entry->id.'/',
				'key'=>'image',
				'width'=>'90',
				'height'=>'90',
				//'rename'=>true, //image by default
				'rename'=>$this->URI->filterParam($entry->title),#$entry->titleHref(not here oups)
				'extensions'=>array('png','jpg'),
				'conversion'=>'png',
				'maxFileSize'=>$maxFileSize
			));/**/#var_dump($maxFileSize);
		});
#exit;
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
