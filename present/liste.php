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
	protected $sqlQuery;
	protected $sqlParams;
	protected $sqlQueryListe;
	function dynamic(){
		parent::dynamic();
		$this->getParamsFromUri();
		$this->buildQuery();
		$this->count();
		$this->pagination();
		$this->liste();
	}
	function getParamsFromUri(){
		$this->uri = view::param(0);
		$this->subUri = (strrpos($this->uri,'s')===strlen($this->uri)-1?substr($this->uri,0,-1):$this->uri);
		$this->page = view::param('page');
	}
	function buildQuery(){
		$this->limit = 5;
		$this->offset = 0;
		$this->sqlQuery = array(
			'where'=>array(
				
			),
		);
		$this->sqlQueryListe = array_merge($this->sqlQuery,array(
			'limit'=>$this->limit,
			'offset'=>$this->offset,
		));
		$this->sqlParams = array();
	}
	function count(){
		$this->count = model::count4D($this->taxonomy,$this->sqlQuery,$this->sqlParams);
	}
	function pagination(){
		$this->pagination = array(
			'prefix'			=>'|page:',
			'maxCols'			=>3,
		);
		
		if($this->page===null)
			$this->page = 1;
		elseif(
			!is_integer(filter_var($this->page,FILTER_VALIDATE_INT))
			||($this->page=(int)$this->page)<2
			||$this->count<=($this->offset=($this->page-1)*$this->limit)
		)
			throw new View_Exception('404');
		
		if(($this->offset+$this->limit)>$this->count)
			$this->pagination->end = $this->count;
		else
			$this->pagination->end = $this->offset+$this->limit;
		
		$this->pagination->pagesTotal = (int)ceil($this->count/$this->limit);
		
		if($this->pagination->maxCols>$this->pagination->pagesTotal)
			$this->pagination->max = $this->pagination->pagesTotal-1;
		else
			$this->pagination->max = $this->pagination->maxCols-1;
			
		$this->pagination->start = $this->page-(int)floor($this->pagination->max/2);
		if(!$this->pagination->start)
			$this->pagination->start = 1;
		$this->pagination->end = ($this->pagination->start+$this->pagination->max)>$this->pagination->pagesTotal?$this->pagination->pagesTotal:$this->pagination->start+$this->pagination->max;
		if($this->pagination->end-$this->pagination->start<$this->pagination->max)
			$this->pagination->start = $this->pagination->end-$this->pagination->max;
	}
	function liste(){
		$this->liste = new ArrayObject(model::table4D($this->taxonomy,$this->sqlQueryListe,$this->sqlParams));
		$this->countListe = count($this->liste);
	}
}
