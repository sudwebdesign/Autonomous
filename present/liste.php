<?php namespace present;
use uri;
use model\Query;
use model\R;
use view;
use view\Exception as View_Exception;
use control\Geocoding;
class liste extends \present{
	use Mixin_Pagination;
	protected $limitation				= 5;
	protected $truncation				= 369;
	protected $limitation2				= 10;
	protected $truncation2				= 60;
	protected $Query;
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);
	}
	function dynamic(){
		parent::dynamic();
		$uri = $this->URI;
		$this->page = $uri->page;
		$this->limit = $this->limitation;
		$this->offset = ($this->page!=NULL)?(($this->page-1)*$this->limitation):0;
		$this->subUri = (strrpos($uri[0],'s')===strlen($uri[0])-1?substr($uri[0],0,-1):$uri[0]);
		$Query = Query::getNew($this->taxonomy);
		$Query->selectRelationnal([
			'user			<		email',
			'geopoint		>		label',
			'geopoint		>		lat',
			'geopoint		>		lon',
			'geopoint		>		radius',
			'tag			<>		name',
			'tag::thematic	<>		name',
		]);
		
		if(method_exists($this,'addSelect'))
			$this->addSelect($Query);
		
		$i = 0;
		$this->thematics = [];
		$Query->openHavingOr();
		while($tag=$uri[$i+=1]){
			if(is_array($tag)){
				$Query->openHavingAnd();
				foreach($tag as $subTag){
					$Query->joinWhere('tag.name = ? ',[$subTag]);
					$this->thematics[] = $subTag;
				}
				$Query->closeHaving();
			}
			else{
				$Query->joinWhere('tag.name = ? ',[$tag]);
				$this->thematics[] = $tag;
			}
		}
		$Query->closeHaving();
		
		$rad = null;
		$lat = null;
		$lon = null;
		if($uri->geo||($uri->lat&&$uri->lon)){
			$rad = (float)$uri->rad;
			if($uri->lat&&$uri->lon){
				$lat = (float)$uri->lat;
				$lon = (float)$uri->lon;
			}
			else{
				$point = R::findOne('geoname','WHERE name LIKE ?',[str_replace('%','',$uri->geo).'%']);
				if($point){
					$lat = (float)$point->latitude;
					$lon = (float)$point->longitude;
					if(!$rad)
						$rad = (float)$point->radius;
				}
			}
			if($lat!==null&&$lon!==null)
				$Query
					->groupBy('geopoint.lat')
					->groupBy('geopoint.lon')
					->groupBy('geopoint.radius')
					->select("geodistance(geopoint.lat,geopoint.lon,?,?) as distance",[$lat,$lon])
					->orderBy('distance ASC')
				;
			if($rad)
				$Query
					->select('(geodistance(geopoint.lat,geopoint.lon,?,?)+COALESCE(geopoint.radius,0)) as distance2inc',[$lat,$lon])
					->select('(geodistance(geopoint.lat,geopoint.lon,?,?)-COALESCE(geopoint.radius,0)) as distance2touch',[$lat,$lon])
				;
		}
		if($uri->phonemic){
			$Query
				->whereFullText('document',$uri->phonemic,'french')
				->selectFullTextHighlite('presentation',$uri->phonemic,$this->truncation,'french')
				//->selectFullTextHighlight('presentation',$uri->phonemic,'french')
				->orderByFullTextRank('document',$uri->phonemic,'french') 
			;
		}
		else{
			$Query
				->selectTruncation('presentation',$this->truncation)
			;
		}
		$Query
			->select(['id','title','tel','url','created'])
		;
		
		//for PgSql8 (no need in >=PgSql9.3)
		$Query
			->groupBy($this->taxonomy.'.id')
			->groupBy($this->taxonomy.'.title')
			->groupBy($this->taxonomy.'.tel')
			->groupBy($this->taxonomy.'.url')
			->groupBy($this->taxonomy.'.created')
			->groupBy($this->taxonomy.'.presentation')
			->groupBy('"user".id')
			->groupBy('"user".email')
		;
		if($uri->phonemic)
			$Query->groupBy($this->taxonomy.'.document');
		
		if($this->thematics->count())
			$Query
				->select('COUNT(DISTINCT(thematic__tag.name)) as count_tag_rank')
				->where('thematic__tag.name IN ?',[$this->thematics->getArray()])
				->orderBy('count_tag_rank DESC')
			;

		$Query
			->orderBy('created DESC')
		;

		if($rad){
			list($minlon, $minlat, $maxlon, $maxlat) = Geocoding::getBoundingBox([$lat,$lon],$rad,Geocoding::getEarthRadius('km'));
			if($uri->proxima){
				$distance2 = 'touch';
				$Query
					//->where('(geopoint.lat BETWEEN ? AND ?) OR (geopoint.lon BETWEEN ? AND ?)',[$minlat,$maxlat,$minlon,$maxlon])
					->openWhereOr()
					->where('geopoint.minlat BETWEEN ? AND ?',[$minlat,$maxlat])
					->where('geopoint.minlon BETWEEN ? AND ?',[$minlon,$maxlon])
					->where('geopoint.maxlat BETWEEN ? AND ?',[$minlat,$maxlat])
					->where('geopoint.maxlon BETWEEN ? AND ?',[$minlon,$maxlon])
					->closeWhere()
				;
			}
			else{
				$distance2 = 'inc';
				$Query
					->where('geopoint.minlat>=? AND geopoint.maxlat<=? AND geopoint.minlon>=? AND geopoint.maxlon<=?',[$minlat,$maxlat,$minlon,$maxlon])
				;
			}
			
			//mysql - so simple - non sql standard
			//$Query->joinWhere("distance2{$distance2} <= ?",[$rad]);

			//pgsql - more complex - non sql standard
			$Query = Query::getNew()
				->with("view AS ({$Query})",$Query->getParams())
				->select('*')
				->from('view')
				->where("distance2{$distance2} <= ?",[$rad])
				->limit($this->limit,$this->offset)
			;
		}
		else{
			$Query
				->limit($this->limit,$this->offset)
			;
		}

		$this->count = $Query->countAll();
		$this->pagination();

		$this->liste = $Query->tableMD();
		$this->countListe = count($this->liste);
		//exit($this->liste);
		
		//sub flux
		$subCategories = ['evenement','ressource','projet','association','annonce','mediatheque'];
		unset($subCategories[array_search($this->taxonomy,$subCategories)]);
		$full = [];
		$full = array_merge($full,$this->thematics->getArray());
		if($uri->phonemic)
			$full[] = $uri->phonemic;
		if($uri->geo)
			$full[] = $uri->geo;
		$full = implode(' ',$full);
		$XQuery2 = [];
		$XQuery2P = [];
		foreach($subCategories as $cat){
			if(!Query::tableExists($cat))
				continue;
			$Query2 = Query::getNew()
				->select(['id','pg_class.relname AS table','title','created'])
				->limit($this->limitation2)
				->from($cat.'","'.'pg_class')
				->where($cat.'.tableoid = pg_class.oid')
			;
			if($full)
				$Query2
					->whereFullText('document',$uri->phonemic,'french')
					->selectFullTextHighlite('presentation',$full,$this->truncation2,'french',['StartSel' => '"<b>"', 'StopSel' => '"</b>"'])
				;
			else
				$Query2->selectTruncation('presentation',$this->truncation2);
			$XQuery2[] = "($Query2)";
			$XQuery2P = array_merge($XQuery2P,$Query2->getParams());
		}
		if(!empty($XQuery2)){
			$this->liste2 = R::getAll(implode(' UNION ',$XQuery2),$XQuery2P);
			$subCatSea = ['evenement','ressource','projet','association','annonce','mediatheque'];	
			$urlSubCat = ['Événement','Ressource','Projet','Association','Annonce','Médiathèque'];
			for ($l2=0;$l2<count($this->liste2);$l2++)
				$this->liste2[$l2]['table'] = str_replace($subCatSea,$urlSubCat,$this->liste2[$l2]['table']);
			//exit($this->liste);		
		}
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
