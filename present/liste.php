<?php namespace present;
use uri;
use view;
use model\Query;
use model\R;
use view\Exception as View_Exception;
class liste extends \present{
	use Mixin_Pagination;
	protected $limitation				= 5;
	protected $truncation				= 369;
	protected $Query;
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
		$this->Query = Query::getNew($this->taxonomy);
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
		
		$rad = null;
		if($uri->geo||($uri->lat&&$uri->lon)){
			$rad = (float)$uri->rad;
			if($uri->lat&&$uri->lon){
				$lat = (float)$uri->lat;
				$lon = (float)$uri->lon;
			}
			else{
				$point = R::findOne('geoname','WHERE name LIKE ?',[str_replace('%','',$uri->geo).'%']);
				$lat = (float)$point->latitude;
				$lon = (float)$point->longitude;
				if(!$rad)
					$rad = (float)$point->radius;
			}
			$this->Query
				->groupBy('geopoint.lat')
				->groupBy('geopoint.lon')
				->groupBy('geopoint.radius')
				->select('geodistance(geopoint.lat,geopoint.lon,?,?) as distance',[$lat,$lon])
				->select('(geodistance(geopoint.lat,geopoint.lon,?,?)+COALESCE(geopoint.radius,0)) as distance2inc',[$lat,$lon])
				->select('(geodistance(geopoint.lat,geopoint.lon,?,?)-COALESCE(geopoint.radius,0)) as distance2touch',[$lat,$lon])
				->orderBy('distance ASC')
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
			->select(['id','title','tel','url','created'])
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

		if($rad){
			$distance2 = $uri->proxima?'touch':'inc';
			//$this->Query->joinWhere("distance2{$distance2} <= ?",[$rad]); //mysql
			$this->Query = Query::getNew()
				->with("view AS ({$this->Query})",$this->Query->getParams())
				->select('*')
				->from('view')
				->where("distance2{$distance2} <= ?",[$rad])
				->limit($this->limit,$this->offset)
			;
		}
		else
			$this->Query
				->limit($this->limit,$this->offset)
			;

		$this->count = $this->Query->countAll();
		$this->pagination();

		$this->liste = $this->Query->tableMD();
		//exit($this->liste);

		$this->countListe = count($this->liste);
		$this->h1 = $uri[0];
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}
	function imageByItem($item){
		return '/content/'.$this->taxonomy.'/'.$item->id.'/'.uri::filterParam($item->title).'.png';
	}
}