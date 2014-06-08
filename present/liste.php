<?php namespace present;
use view;
use model;
class liste extends \present{
	static $taxonomy;
	static function compileVars(&$vars=array()){
		
	}
	static function compileElement(){
		
	}
	static function exec(){
		static::$taxonomy = end(self::$options['namespaces']);
	}	
	static function execVars(&$vars=array()){
		$table = static::$taxonomy;
		$query = array(
			'where'=>array(
				
			),
			'limit'=>20,
			'offset'=>null,
		);
		$params = array();
		return array(
			'count'=>model::count4D($table,$query,$params),
			'liste'=>model::table4D($table,$query,$params),
			'taxonomy'=>static::$taxonomy,
		);
	}
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
	static function pageCurrent(){
		//if(	$v&&(
			//||($v=(int)$v)<2
			//||$this->rowsTotal<=($offset=($v-1)*$this->rowByPage)
		//))
			//throw new E_V('404');
		return is_integer(filter_var($v=view::param('page'),FILTER_VALIDATE_INT))?$v:null;
	}
	static function handleParams(){
		//$params = view::param();
		//$this->h1 = $this->title = $this->uri = array_shift($params);
		//$this->subUri = (strrpos($this->uri,'s')===strlen($this->uri)-1?substr($this->uri,0,-1):$this->uri);
		//foreach($params as $k=>$v){
			//if(is_integer($k)){
				//recherche intelligente
				//filtrage taxonomique
				//recherche par relations taxonomique
				//recherche text (title,presentation)
			//}
			//else{
				//switch($k){
					//default:
						//return $this->error();
					//break;
					//case 'geo': //nominatim
						//
					//break;
					//case 'commune':
						//
					//break;
					//case 'point':
						//
					//break;
					//case 'rayon':
						//
					//break;
					//case 'page':
						//if(
							//!is_integer(filter_var($v,FILTER_VALIDATE_INT))
							//||($v=(int)$v)<2
							//||$this->rowsTotal<=($offset=($v-1)*$this->rowByPage)
						//)
							//return $this->error();
						//$this->pageCurrent = $v;
						//$this->paginationOffset = $offset;
					//break;
				//}
			//}
		//}
	}
	static function preparePagination(){
		//self::$pagePrefix = ($this->uri?$this->pageSeparator:'').$this->pagePrefixer;
		//$this->end = ($this->paginationOffset+$this->rowByPage)>$this->rowsTotal?$this->rowsTotal:$this->paginationOffset+$this->rowByPage;
		//$this->pagesTotal = (int)ceil($this->rowsTotal/$this->rowByPage);
		//$this->max = ($this->maxPaginationColumns>$this->pagesTotal?$this->pagesTotal:$this->maxPaginationColumns)-1;
		//$this->start = ($this->start=$this->pageCurrent-(int)floor($this->max/2))>1?$this->start:1;
		//$this->end = ($this->start+$this->max)>$this->pagesTotal?$this->pagesTotal:$this->start+$this->max;
		//if($this->end-$this->start<$this->max)
			//$this->start = $this->end-$this->max;
	}
}
