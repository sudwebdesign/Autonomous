<?php namespace Presenter;
use Uri;
use View;
use Model;
use Model\Query;
use Core\Session;
use Core\Dev;
class Item extends Presenter{
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
			if(filter_var($uri[1],FILTER_VALIDATE_INT)){
				$redirect = $q->select('titleHref')->where('id=?',[$uri[1]])->getCell();
				$this->redirect($redirect,$uri[1]);
			}
			else{
				$redirect = $q->select('id')->where('"titleHref"=?',[$uri[1]])->getCell();
				if($redirect){
					$this->redirect($redirect);
				}
			}
			exit;
		}
		$this->Query = (new Query($this->taxonomy))
			->where('"'.$this->prefix.$this->taxonomy.'"'.'.id=?',[$uri[2]])
		;
		$this->item = $this->Query->row4D();#2 warnings & 404 if no tag & geopoint linked with item else OK
		
		if(empty($this->item))
			$this->getView()->getController()->error(404);
		if(!$this->item->titleHref){
			$this->item->titleHref = $this->URI->filterParam($this->item->title);
		}
		if($uri[1]!=$this->item->titleHref){
			#var_dump('<h1>unlimited redirected loop detected</h1>',$uri[1],$this->item->titleHref);exit;
			$this->redirect($this->item->titleHref);
		}
		$this->img = $this->imageByItem();
		$this->files = $this->filesByItem();
		$this->item->atitle = htmlspecialchars($this->item->title, ENT_COMPAT);
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
		if(!Dev::has(Dev::ROUTE))
			header('Location: '.$redirect,true,301);
		else
			echo 'Location: '.$redirect;
		exit;
	}
}
