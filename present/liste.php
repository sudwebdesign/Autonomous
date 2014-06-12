<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
use surikat\view\Exception as View_Exception;
class liste extends \present{
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);
		
	}
	function dynamic(){
		parent::dynamic();
		//$pagePrefixer = 'page:';
		//$pagePrefix = '';
		//$pageCurrent = 1;
		//$paginationOffset = 0;
		//$rowByPage = 5;
		//$maxPaginationColumns = 3;
		//$pageSeparator = '|';
		//$page = view::param('page');
		//if(($page&&!is_integer(filter_var($page,FILTER_VALIDATE_INT)))
			//||($page=(int)$page)<2
			//||$this->rowsTotal<=($offset=($page-1)*$this->rowByPage)
		//)
			//throw new View_Exception('404');
		//$pageCurrent = $page;
		//$paginationOffset = $offset;
		//$pagePrefix = ($uri?$pageSeparator:'').$pagePrefixer;
		//$end = ($paginationOffset+$rowByPage)>$rowsTotal?$rowsTotal:$paginationOffset+$rowByPage;
		//$pagesTotal = (int)ceil($rowsTotal/$rowByPage);
		//$max = ($maxPaginationColumns>$pagesTotal?$pagesTotal:$maxPaginationColumns)-1;
		//$start = ($start=$pageCurrent-(int)floor($max/2))>1?$start:1;
		//$end = ($start+$max)>$pagesTotal?$pagesTotal:$start+$max;
		//if($end-$start<$max)
			//$start = $end-$max;
		//$this->page = $page;
		$table = $this->taxonomy;
		$query = array(
			'where'=>array(
				
			),
		);
		$queryListe = array_merge($query,array(
			'limit'=>$limit=20,
			'offset'=>null,
		));
		$params = array();
		$this->count = model::count4D($table,$query,$params);
		$this->liste = new ArrayObject(model::table4D($table,$queryListe,$params));
		$this->countListe = count($this->liste);
		$this->uri = view::param(0);
		$this->subUri = (strrpos($this->uri,'s')===strlen($this->uri)-1?substr($this->uri,0,-1):$this->uri);
	}
}
