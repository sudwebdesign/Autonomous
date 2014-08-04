<?php namespace present;
use URI;
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
	protected $limitation				= 10;
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);
	}
	function dynamic(){
		parent::dynamic();
		$this->page = uri::param('page');
		$this->uri = $this->URI;
		$this->subUri = (strrpos($this->URI,'s')===strlen($this->URI)-1?substr($this->URI,0,-1):$this->URI);
		$this->imgDir = 'content/'.$this->taxonomy.'/';

		$this->Query = model::newFrom($this->taxonomy);
		$this->Query->selectRelationnal(array(
			'user			<		email',
			'date			>		start',
			'date			>		end',
			'tag			<>		name',
			'taxonomy		<>		name',
			//'taxonomy		<> 		taxonomy <>	name',
			//'tag::thematics	<>		name',
			//'taxonomy		<> 		taxonomy::thematics <>	name',
		));
		//$this->Query = model::newFrom4D($this->taxonomy);
		
		$uri = view::getUri();
		$uri->resolveMap(array(
			':int'=>function($param){
				return R::load('taxonomy',$param);
			},
			'geo',
			'search'=>true,
		));
		//$this->taxonomies = array();
		//$redirect = '';
		//foreach($this->finders as $fr){
			//if(!isset($this->assocParams[$fr]))
				//continue;
			//$this->assocParams[$fr]->sort(SORT_NATURAL|SORT_FLAG_CASE);
			//$redirect .= implode('|',(array)$this->assocParams[$fr]).'|';
		//}
		$this->keywords = array();
		//$i = 0;
		//while(($param = uri::param($i+=1))!==null){
			//$this->keywords[] = $param;
			//$this->uri .= '|'.$param;
		//}
		$this->find = array();
		//foreach(array('taxonomy','locality','tag') as $t){
			//$k = $t.'Id';
			//if(!empty($this->find->$k))
				//$this->Query->joinWhere($t.'.id IN ?',array((array)$this->find->$k));
		//}
		//
		if($uri->search)
			$this->Query->whereFullText('document',$uri->search);
		$this->Query
			->select(array('title','tel','url'))
			//->selectTruncation('presentation',369)
			->selectFullTextHighlite('presentation',$uri->search,369)
			->orderByFullTextRank('document',$uri->search)
			->select('created')
		;

		$this->count = $this->Query->count();
		
		$this->pagination();
		
		$this->liste = $this->Query->limit($this->limit,$this->offset)->tableMD();
		//exit(print($this->liste));
		$this->countListe = count($this->liste);
		
		$this->h1 = uri::param(0);
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}
	function imageByItem($item){
		return $this->imgDir.$item->id.'/'.uri::filterParam($item->title).'.png';
	}
}