<?php namespace Presenter;
use DependencyInjection\Registry;
use FileSystem\FS;
use FileSystem\Uploader;
use Geo\Geocoding;
use Model;
use Model\R;
use Model\Query;
use Model\RedBeanPHP\Database;
use Model\Exception_Validation;
use SourceCode\PHP;
use Tool\Deleter;
use Uri;
use User\Post;
use User\Session;
use Validation\Filter;
class Delivrer extends Presenter{
	function assign(){
		parent::assign();
		$this->taxonomy = lcfirst(end($this->presentNamespaces));
	}
	function dynamic(){
		parent::dynamic();
		$uri = $this->URI;
		if(!filter_var($uri[2],FILTER_VALIDATE_INT)){
			$q = new Query($this->taxonomy);
			if(filter_var($uri[1],FILTER_VALIDATE_INT)&&($redirect = $q->select('titleHref')->where('id=?',[$uri[1]])->getCell()))
				$this->redirect($redirect,$uri[1]);
			elseif($redirect = $q->select('id')->where('"titleHref"=?',[$uri[1]])->getCell())
				$this->redirect($redirect);
			exit;
		}
		$this->Query = (new Query($this->taxonomy))
			->where('"'.$this->taxonomy.'"'.'.id=?',[$uri[2]])
		;
		$this->item = $this->Query->row4D();
		if(!$this->item->titleHref)
			$this->item->titleHref = $this->URI->filterParam($this->item->title);
		if($uri[1]!=$this->item->titleHref)
			$this->redirect($this->item->titleHref);
		$this->img = $this->imageByItem();
		$this->files = $this->filesByItem();
		$this->item->atitle = htmlspecialchars($this->item->title, ENT_COMPAT);
		$this->POST($uri[2]);
		
	}
	function imageByItem($item=null){
		if(!isset($item))
			$item = $this->item;
		return 'content/'.$this->taxonomy.'/'.$item->id.'/'.$this->URI->filterParam($item->title).'.png';
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
		if(!$this->getDependency('Dev\Level')->ROUTE)
			header('Location: '.$redirect,true,301);
		else
			echo 'Location: '.$redirect;
		exit;
	}
	function POST($id){
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$this->postDeleted = true;
		$type = $this->taxonomy;#
		try{
			$entry = $this->POST_Common($type,$id);
			if(method_exists($this,'POST_Specifications'))
				$this->POST_Specifications($entry);
			if($entry){
				R::trash($entry);
				Deleter::alls([
					'dir'=>'content/'.$type.'/'.$id,
					'deletion'=>true
				]);
			}
			Post::clearPersistance();
		}
		catch(Exception_Validation $e){
			$this->formErrors = $e->getFlattenData();
			$this->formPosted = false;
			$this->postDeleted = false;
		}
	}
	function POST_Common($type,$id){
		$entry = R::findOne($type,'id='.$id);
		$user = $this->userSessionEmail;
		if($user&&$entry->user_id){
			#$user = R::findOne('user','id='.$entry->user_id);FLDB
			$user = R::findOne('user','email='.$user);
			$entry->user = $user;
		}
		else
			$entry->error('user','required',true);
		$P = Post::getObject();
		$entry->deletion = $P->deletion;
		$entry->validated = $P->validate;
		return $entry;
	}

}
