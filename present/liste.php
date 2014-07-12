<?php namespace present;
use view;
use model;
use model\Query;
use model\Query4D;
use model\R;
use control\str;
use model\Table_Taxonomy as taxonomy;
use model\Table_Tag as tag;
use model\Table_Locality as locality;
use control\ArrayObject;
use view\Exception as View_Exception;
class liste extends \present{
	use Mixin_Pagination;
	
	protected $limit				= 5;
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
		
		$this->page = view::param('page');
		$this->uri = $this->URI;
		$this->subUri = (strrpos($this->URI,'s')===strlen($this->URI)-1?substr($this->URI,0,-1):$this->URI);

		$this->query = new Query4D($this->taxonomy);

		$uriA = view::param();
		$orderParams = array(
			'(int)',
			'geo',
			'search',
		);
		$this->taxonomies = array();
		foreach($orderParams as $logic){
			switch($logic){
				case '(int)':
					foreach($uriA as $k=>$a)
						if(is_integer($k)){
							$this->taxonomies[] = $uriA[$k];
							unset($uriA[$k]);
						}
						else
							break;
					
				break;
				case 'search':
					
				break;
			}
		}
		foreach($uriA as $k=>$a){
			if(is_integer($k)){
				//if(!in_array()
				//$orderParamsFact[] = $k;
			}
			else{
				switch($k){
					case '':
					break;
					case '':
					break;
					case '':
					break;
					case '':
					break;
					case '':
					break;
					default:
					break;
				}
			}
		}
		$redirect = '';//var_dump($this->finders,$this->assocParams);exit;
		foreach($this->finders as $fr){
			if(!isset($this->assocParams[$fr]))
				continue;
			$this->assocParams[$fr]->sort(SORT_NATURAL|SORT_FLAG_CASE);
			$redirect .= implode('|',(array)$this->assocParams[$fr]).'|';
		}
		$redirect = trim($redirect,'|');
		if(trim(implode('|',(array)$this->keywords),'|')!=$redirect)
			header('Location: '.$this->HREF.'|'.$redirect,true,301);
			
		//var_dump();exit;
		$this->keywords = array();
		$i = 0;
		while(($param = view::param($i+=1))!==null){
			$this->keywords[] = $param;
			$this->uri .= '|'.$param;
		}



		$this->findMotorParams(); //IA
		$this->findMotorCompo();
		$this->countAll();
		$this->pagination();
		
		$this->liste();
		$this->findSrcImageItems();
		$this->h1 = view::param(0);
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}

	protected $finders = array(
		'taxonomyId',
		'localityId',
		'tagId',
		'texts',
	);
	protected function findMotorCompo(){
		//exit($s->debug());
		$this->pushJoinWhereFind(array(
			'taxonomy',
			'locality',
			'tag',
		));
		$q = '"';
		foreach((array)$this->find->texts as $t){
			$cols = array(
				'title',
				'presentation',
			);

			//pgsql full text search
			foreach($cols as $k=>$col)
				$cols[$k] = "to_tsvector({$q}{$this->taxonomy}{$q}.{$q}{$col}{$q})";
			$this->sqlQuery['where'][] = implode(' || ',$cols)." @@ to_tsquery(?)";
			$this->sqlParamsWhere[] = "'$t'";
		}
		$this->sqlQuery['select'][] = 'title';
		$this->sqlQuery['select'][] = 'tel';
		$this->sqlQuery['select'][] = 'url';

		//$this->sqlQuery['select'][] = $q.'locality'.$q.'.'.$q.'id'.$q.' as '.$q.'locality<>id'.$q;
		//$this->sqlQuery['select'][] = $q.'locality'.$q.'.'.$q.'label'.$q.' as '.$q.'locality<>label'.$q;
		//$this->sqlQuery['join'][] = 'LEFT OUTER JOIN locality on geopoint.locality_id=locality.id';
		//$this->sqlQuery['group_by'][] = $q.'locality'.$q.'.'.$q.'id'.$q;
		
		$this->selectTruncation('presentation',369);
	}
	protected function selectTruncation($col,$truncation=369){
		$q = '"';
		$c = $q.$this->taxonomy.$q.'.'.$q.$col.$q;
		$this->sqlQuery['select'][] = "SUBSTRING($c,1,$truncation) as $col";
		$this->sqlQuery['select'][] = "LENGTH($c) as {$col}_length";
		$this->sqlQuery['select'][] = 'created';
	}
	protected function sqlParams(){
		return array_merge($this->sqlParamsWhere,$this->sqlParamsJoinWhere);
	}
	protected function pushJoinWhereFind($table){ //helper method for findMotorCompo
		if(is_array($table)){
			foreach($table as $t)
				$this->pushJoinWhereFind($t);
			return;
		}
		$k = $table.'Id';
		if(!empty($this->find->$k))
			$this->pushJoinWhere($table.'.id IN(?)',$this->find->$k);
	}
	protected function pushJoinWhere($query,$params){ //helper method for findMotorCompo and pushJoinWhereFind
		$this->sqlQuery['joinWhere'][] = Query::multiSlots($query,(array)$params);
		foreach($params as $k=>$v)
			if(is_integer($k))
				$this->sqlParamsJoinWhere[] = $v;
			else
				$this->sqlParamsJoinWhere[$k] = $v;
	}
	protected function findMotorParams(){
		$this->assocParams = array();
		$this->find = array();
		$find =& $this->find;#var_dump($this->keywords,$this->find,$this->assocParams);exit;
		foreach($this->keywords as $k){
			foreach($this->finders as $fr){
				$m = 'keyword'.ucfirst($fr);
				$rewrite = null;
				if($found=$this->$m($k,$rewrite)){
					if(!isset($find->$fr))
						$find->$fr = array();
					$find->{$fr}[] = $found;
					if(!isset($this->assocParams[$fr]))
						$this->assocParams[$fr] = array();
					if($rewrite!==null)
						$k = (string)$rewrite;
					if(!$this->assocParams[$fr]->in($k)) //doublon
						$this->assocParams[$fr][] = $k;
						
					break;
				}
			}
		}
		$redirect = '';//var_dump($this->finders,$this->assocParams);exit;
		foreach($this->finders as $fr){
			if(!isset($this->assocParams[$fr]))
				continue;
			$this->assocParams[$fr]->sort(SORT_NATURAL|SORT_FLAG_CASE);
			$redirect .= implode('|',(array)$this->assocParams[$fr]).'|';
		}
		$redirect = trim($redirect,'|');
		if(trim(implode('|',(array)$this->keywords),'|')!=$redirect)
			header('Location: '.$this->HREF.'|'.$redirect,true,301);
	}
	protected function keywordTaxonomyId($k,&$rewrite){
		return taxonomy::findRewrite($k,$rewrite);
	}
	protected function keywordLocalityId($k,&$rewrite){
		return locality::findRewrite($k,$rewrite);
	}
	protected function keywordTagId($k,&$rewrite){
		return tag::findRewrite($k,$rewrite);
	}
	protected function keywordTexts($k){
		return $k;
	}
	
	protected function countAll(){
		$this->count = $this->query->count($this->taxonomy,$this->sqlQuery,$this->sqlParams());
	}
	
	protected function liste(){
		$this->sqlQueryListe = array_merge($this->sqlQuery,array(
			'limit'=>$this->limit,
			'offset'=>$this->offset,
		));
		$this->liste = $this->query->table($this->taxonomy,$this->sqlQueryListe,$this->sqlParams());
		$this->countListe = count($this->liste);
	}
	protected function findSrcImageItems(){
		$this->imgsItems=NULL;
		foreach($this->liste as $item){
			$imgFolder = 'content/'.substr(str::unaccent(str::tolower(view::param(0))),0,-1).'/'.$item->id.'/';
			$imgName = str_replace(' ','-',$item->title);
			$imgsItem = glob($imgFolder."{".$imgName.".*}", GLOB_BRACE);
			$this->imgsItems[$item->id] = $imgsItem;
		}
	}
}