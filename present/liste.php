<?php namespace present;
use view;
use model;
use model\Table_Taxonomy as taxonomy;
use model\Table_Tag as tag;
use model\Table_Locality as locality;
use surikat\control\ArrayObject;
use surikat\view\Exception as View_Exception;
class liste extends \present{
	protected $limit				= 1;
	protected $offset	    		= 0;
	protected $sqlQuery				= array(
										'where'=>array(),
										'joinWhere'=>array()
									);
	protected $sqlParamsWhere		= array();
	protected $sqlParamsJoinWhere	= array();
	protected $sqlQueryListe;
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);
		
	}
	function dynamic(){
		parent::dynamic();
		$this->getParamsFromUri();
		$this->searchMotorParams();
		$this->searchMotorCompo();
		$this->countAll();
		$this->pagination();
		$this->liste();

		$this->h1 = view::param(0);
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}
	
	protected function getParamsFromUri(){
		$this->page = view::param('page');
		$this->uri = $this->URI;
		$this->keywords = array();
		$i = 0;
		while(($param = view::param($i+=1))!==null){
			$this->keywords[] = $param;
			$this->uri .= '|'.$param;
		}
		$this->subUri = (strrpos($this->URI,'s')===strlen($this->URI)-1?substr($this->URI,0,-1):$this->URI);
	}

	protected function searchMotorCompo(){
		//exit($s->debug());
		$this->pushJoinWhereSearch(array(
			'taxonomy',
			'locality',
			'tag',
		));
		$q = model::getQuote();
		foreach((array)$this->search->texts as $t){
			$cols = array(
				'title',
				'presentation',
			);
			foreach($cols as $k=>$col)
				$cols[$k] = "to_tsvector({$q}{$this->taxonomy}{$q}.{$q}{$col}{$q})";
			$this->sqlQuery['where'][] = implode(' || ',$cols)." @@ to_tsquery(?)";
			$this->sqlParamsWhere[] = "'$t'";
		}
	}
	protected function sqlParams(){
		return array_merge($this->sqlParamsWhere,$this->sqlParamsJoinWhere);
	}
	protected function pushJoinWhereSearch($table){ //helper method for searchMotorCompo
		if(is_array($table)){
			foreach($table as $t)
				$this->pushJoinWhereSearch($t);
			return;
		}
		$k = $table.'Id';
		if(!empty($this->search->$k))
			$this->pushJoinWhere($table.'.id IN(?)',$this->search->$k);
	}
	protected function pushJoinWhere($query,$params){ //helper method for searchMotorCompo and pushJoinWhereSearch
		$this->sqlQuery['joinWhere'][] = model::multiSlots($query,(array)$params);
		foreach($params as $k=>$v)
			if(is_integer($k))
				$this->sqlParamsJoinWhere[] = $v;
			else
				$this->sqlParamsJoinWhere[$k] = $v;
	}
	protected $searchers = array(
		'taxonomyId',
		'localityId',
		'tagId',
		'texts',
	);
	protected function searchMotorParams(){
		$this->assocParams = array();
		$this->search = array();
		$search =& $this->search;
		$order = array();
		foreach($this->keywords as $k){
			foreach($this->searchers as $sr){
				if(!isset($search->$sr))
					$search->$sr = array();
				$m = 'keyword'.ucfirst($sr);
				if($found=$this->$m($k)){
					$search->{$sr}[] = $found;
					if(!isset($this->assocParams[$sr]))
						$this->assocParams[$sr] = array();
					$this->assocParams[$sr][] = $k;
					if(!in_array($sr,$order))
						$order[] = $sr;
					break;
				}
			}
		}
		$ordered = array_filter((array)$this->searchers,function($v)use($order){
			return in_array($v,$order);
		});
		if(count(array_diff_assoc(array_values($order),array_values($ordered)))){
			$redirect = '';
			foreach($ordered as $sr)
				$redirect .= implode('|',(array)$this->assocParams[$sr]).'|';
			$redirect = '|'.trim($redirect,'|');
			header('Location: '.$this->HREF.$redirect,true,301);
		}
	}
	protected function keywordTaxonomyId($k){
		return array_search($k,taxonomy::getLabels());
	}
	protected function keywordLocalityId($k){
		return locality::find($k,1);
	}
	protected function keywordTagId($k){
		return tag::find($k,1);
	}
	protected function keywordTexts($k){
		return $k;
	}
	
	protected function countAll(){
		$this->count = model::count4D($this->taxonomy,$this->sqlQuery,$this->sqlParams());
	}
	protected function pagination(){
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
	protected function liste(){
		$this->sqlQueryListe = array_merge($this->sqlQuery,array(
			'limit'=>$this->limit,
			'offset'=>$this->offset,
		));
		$this->liste = new ArrayObject(model::table4D($this->taxonomy,$this->sqlQueryListe,$this->sqlParams()));
		$this->countListe = count($this->liste);
	}
}
