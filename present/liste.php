<?php namespace present;
use view;
use model;
use model\Table_Taxonomy as taxonomy;
use model\Table_Tag as tag;
use model\Table_Locality as locality;
use surikat\control\ArrayObject;
use surikat\view\Exception as View_Exception;
class liste extends \present{
	protected $limit		= 5;
	protected $offset	    = 0;
	protected $sqlQuery		= array('where'=>array(),'joinWhere'=>array());
	protected $sqlParams	= array();
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
		$this->count();
		$this->pagination();
		$this->liste();
	}
	
	function getParamsFromUri(){
		$this->uri = view::param(0);
		$this->subUri = (strrpos($this->uri,'s')===strlen($this->uri)-1?substr($this->uri,0,-1):$this->uri);
		$this->page = view::param('page');

		$this->keywords = array();
		$i = 0;
		while(($param = view::param($i+=1))!==null)
			$this->keywords[] = $param;
		
		//var_dump($this->keywords);exit;
	}
	function searchMotorParams(){
		$this->search = array();
		$s =& $this->search;

		$s->taxonomyId = array();
		$s->tagId = array();
		$s->localityId = array();
		$s->texts = array();

		$taxonomies = taxonomy::getLabels();
		
		foreach($this->keywords as $k){
			if(in_array($k,$taxonomies))
				$s->taxonomyId[] = array_search($k,$taxonomies);
			elseif($id=locality::find($k,1))
				$s->localityId[] = $id;
			elseif($id=tag::find($k,1))
				$s->tagId[] = $id;
			else
				$s->texts[] = $k;
		}


	}
	
	function pushJoinWhere($query,$params){ //helper method for searchMotorCompo and pushJoinWhereSearch
		$this->sqlQuery['joinWhere'][] = model::multiSlots($query,(array)$params);
		foreach($params as $k=>$v)
			if(is_integer($k))
				$this->sqlParams[] = $v;
			else
				$this->sqlParams[$k] = $v;
	}
	function pushJoinWhereSearch($table){ //helper method for searchMotorCompo
		if(is_array($table)){
			foreach($table as $t)
				$this->pushJoinWhereSearch($t);
			return;
		}
		$s =& $this->search;
		$k = $table.'Id';
		if(!empty($s->$k))
			$this->pushJoinWhere($table.'.id IN(?)',$s->$k);
	}
	function searchMotorCompo(){
		//exit($s->debug());
		$this->pushJoinWhereSearch(array(
			'taxonomy',
			'locality',
			'tag',
		));
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
		$this->sqlQueryListe = array_merge($this->sqlQuery,array(
			'limit'=>$this->limit,
			'offset'=>$this->offset,
		));
		$this->liste = new ArrayObject(model::table4D($this->taxonomy,$this->sqlQueryListe,$this->sqlParams));
		$this->countListe = count($this->liste);
	}
}
