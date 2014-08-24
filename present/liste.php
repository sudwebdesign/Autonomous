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
	protected $limitation				= 5;
	protected $truncation				= 369;
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

		$uri = view::getUri();
		$this->Query = model::newFrom($this->taxonomy);
		$this->Query->selectRelationnal([
			'user			<		email',
			'date			>		start',
			'date			>		end',
			'tag			<>		name',
			'tag::thematic	<>		name',
			'geopoint		>		label',
		]);

		$i = 0;
		$this->thematics = [];
		$this->Query->openHavingOr();
		while($tag=$uri[$i+=1]){
			if(is_array($tag)){
				$this->Query->openHavingAnd();
				foreach($tag as $subTag){
					$this->Query->joinWhere('tag.name = ? ',[$subTag]);
					$this->thematics[] = $subTag;
				}
				$this->Query->closeHaving();
			}
			else{
				$this->Query->joinWhere('tag.name = ? ',[$tag]);
				$this->thematics[] = $tag;
			}
		}
		$this->Query->closeHaving();
		
		if($uri->geo||($uri->lat&&$uri->lon)){
			//R::debug(true,2);
			$this->Query
				->select('geodistance(geopoint.point,POINT(?,?))+geopoint.radius as distance',[$uri->lat,$uri->lon])
				->select('geodistance(geopoint.point,POINT(?,?))-geopoint.radius as proximity',[$uri->lat,$uri->lon])
				->orderBy('distance ASC')
			;
			if($uri->rad)
				$this->Query
					->where('distance <= ?',[$uri->rad])
				;
		}
		
		if($uri->phonemic){
			$this->Query
				->whereFullText('document',$uri->phonemic)
				->selectFullTextHighlite('presentation',$uri->phonemic,$this->truncation,'french')
				//->selectFullTextHighlight('presentation',$uri->phonemic,'french')
				->orderByFullTextRank('document',$uri->phonemic)
			;
		}
		else{
			$this->Query
				->selectTruncation('presentation',$this->truncation)
			;
		}
		$this->Query
			->select(array('title','tel','url'))
			->select('created')
		;

		if($this->thematics->count())
			$this->Query
				->select('COUNT(DISTINCT(thematic__tag.name)) as count_tag_rank')
				->where('thematic__tag.name IN ?',[$this->thematics->getArray()])
				->orderBy('count_tag_rank DESC')
			;

		$this->Query
			->orderBy('created DESC')
		;

		$this->count = $this->Query->count();
		$this->pagination();
		$this->liste = $this->Query->limit($this->limit,$this->offset)->tableMD();
		//exit($this->liste);
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