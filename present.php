<?php
class present extends surikat\present{
	#<workflow>
	static function compileVars(&$vars=array()){
		return array(
			'title'=>(($u=view::param(0))?$u.' - ':'').'Autonomie et Partage',
			'timeCompiled'=>time(),
			'communesByDefDpt'=>model::getCommunesByDefDpt(),
			'taxonomyRessources'=>model::getTaxonomy('Ressource'),
			'taxonomyEvenements'=>model::getTaxonomy('Évènement'),
		);
	}
	//static function compileElement(){
		//
	//}	
	static function exec(){
		if(!session_id())
			session_start();
	}
	static function execVars(&$vars=array()){
		return array('time'=>time());
	}
	#</workflow>
		


/*
	const pagePrefixer = 'page:';
	static $pagePrefix = '';
	static $pageCurrent = 1;
	static $paginationOffset = 0;
	static $max;
	static $end;
	static $start;
	static $pagesTotal;
	static $rowByPage = 5;
	static $maxPaginationColumns = 3;
	static $pageSeparator = '|';
	static function pagination(){
		return ;
	}
	static function liste(){
		return array();
	}
	static function rowsTotal(){
		
	}
	static function uri(){
		return ;
	}
	static function subUri(){
		return V::Param(0);
	}
	static function title(){
		return V::Param(0);
	}
	static function pageCurrent(){
		//if(	$v&&(
			//!
			//||($v=(int)$v)<2
			//||$this->rowsTotal<=($offset=($v-1)*$this->rowByPage)
		//))
			//throw new E_V('404');
		return is_integer(filter_var($v=V::Param('page'),FILTER_VALIDATE_INT))?$v:null;
	}
class P_Liste extends P{
	
	//errors
	public $error;

	//links
	public $uri = '';
	public $subUri = '';

	//content auto defined helper
	public $title = '';
	public $h1 = '';
	//config

	//pagination

	
	protected function error($e=404){
		$this->error = $e;
	}
	function __construct($table,$config=array()){		
		parent::__construct($this->data,$config);
		$this->__invoke = C_Liste::construct($table);
		$this->handleParams();
		$this->preparePagination();
	}
	private function handleParams(){
		$params = V::Param();
		$this->h1 = $this->title = $this->uri = array_shift($params);
		$this->subUri = (strrpos($this->uri,'s')===strlen($this->uri)-1?substr($this->uri,0,-1):$this->uri);
		foreach($params as $k=>$v){
			if(is_integer($k)){
				//recherche intelligente
				//filtrage taxonomique
				//recherche par relations taxonomique
				//recherche text (title,presentation)
			}
			else{
				switch($k){
					default:
						return $this->error();
					break;
					case 'geo': //nominatim
						
					break;
					case 'commune':
						
					break;
					case 'point':
						
					break;
					case 'rayon':
						
					break;
					case 'page':
						if(
							!is_integer(filter_var($v,FILTER_VALIDATE_INT))
							||($v=(int)$v)<2
							||$this->rowsTotal<=($offset=($v-1)*$this->rowByPage)
						)
							return $this->error();
						$this->pageCurrent = $v;
						$this->paginationOffset = $offset;
					break;
				}
			}
		}
	}
	static function preparePagination(){
		self::$pagePrefix = ($this->uri?$this->pageSeparator:'').$this->pagePrefixer;
		$this->end = ($this->paginationOffset+$this->rowByPage)>$this->rowsTotal?$this->rowsTotal:$this->paginationOffset+$this->rowByPage;
		$this->pagesTotal = (int)ceil($this->rowsTotal/$this->rowByPage);
		$this->max = ($this->maxPaginationColumns>$this->pagesTotal?$this->pagesTotal:$this->maxPaginationColumns)-1;
		$this->start = ($this->start=$this->pageCurrent-(int)floor($this->max/2))>1?$this->start:1;
		$this->end = ($this->start+$this->max)>$this->pagesTotal?$this->pagesTotal:$this->start+$this->max;
		if($this->end-$this->start<$this->max)
			$this->start = $this->end-$this->max;
	}
	static function date($date){
		return $date;
	}
* */
}
