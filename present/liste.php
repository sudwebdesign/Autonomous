<?php namespace present;
use view;
use model;
use surikat\control\ArrayObject;
use surikat\view\Exception as View_Exception;
class liste extends \present{
	static function assign($o){
		
	}
	static function dynamic($o){
		$o->taxonomy = end($o->options->namespaces);
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
		//$o->page = $page;
		$table = $o->taxonomy;
		$query = array(
			'where'=>array(
				
			),
		);
		$queryListe = array_merge($query,array(
			'limit'=>$limit=20,
			'offset'=>null,
		));
		$params = array();
		$o->count = model::count4D($table,$query,$params);
		$o->liste = new ArrayObject(model::table4D($table,$queryListe,$params));
		$o->countListe = count($o->liste);
		$o->uri = view::param(0);
		$o->subUri = (strrpos($o->uri,'s')===strlen($o->uri)-1?substr($o->uri,0,-1):$o->uri);
	}
}
