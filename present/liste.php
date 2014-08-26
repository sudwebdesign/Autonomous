<?php namespace present;
use view;
use model;
use model\Query;
use model\R;
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
		$uri = view::getUri();
		$this->page = $uri->page;
		$this->uri = $this->URI;
		$this->subUri = (strrpos($this->URI,'s')===strlen($this->URI)-1?substr($this->URI,0,-1):$this->URI);
		$this->imgDir = 'content/'.$this->taxonomy.'/';

		$this->Query = model::newFrom($this->taxonomy);
		$this->Query->selectRelationnal([
			'user			<		email',

			'date			>		start',
			'date			>		end',
			'geopoint		>		label',

			'tag			<>		name',
			'tag::thematic	<>		name',
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
			R::debug(true,2);
			$rad = (float)$uri->rad;
			if($uri->lat&&$uri->lon){
				$lat = (float)$uri->lat;
				$lon = (float)$uri->lon;
			}
			else{
				$point = R::findOne('geoname','WHERE name LIKE ?',['%'.str_replace('%','',$uri->geo)]);
				$lat = (float)$point->latitude;
				$lon = (float)$point->longitude;
				if(!$rad)
					$rad = (float)$point->radius;
			}
			$this->Query
				->select('(geodistance(geopoint.point,POINT(?,?))+COALESCE(geopoint.radius,0)) as distance',[$lat,$lon])
				->select('(geodistance(geopoint.point,POINT(?,?))-COALESCE(geopoint.radius,0)) as proximity',[$lat,$lon])
				->orderBy('distance ASC')
			;
			if($rad)
				$this->Query
					//->where('distance <= ?',[$rad])
					//->joinWhere('distance <= ?',[$rad])
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
		$this->h1 = $uri[0];
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}
	function imageByItem($item){
		return $this->imgDir.$item->id.'/'.uri::filterParam($item->title).'.png';
	}
}