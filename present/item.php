<?php namespace present;
use uri;
use view;
use model;
use model\Query;
use control\session;
use control\ArrayObject;
use dev;
class item extends \present{
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);#var_export($this);exit;
	}
	function dynamic(){
		parent::dynamic();
		session::start(); //session auto start when get a key, if output not bufferised but direct flushed, have to start first
		$uri = $this->URI;
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
		#var_dump($this);
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
}
