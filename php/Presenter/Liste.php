<?php namespace Presenter;
use DependencyInjection\Registry;
use HTTP\Domain;
use Exception\View as View_Exception;
use Geo\Geocoding;
use Model\Query;
use Model\R;
use User\Session;
//$this->getDependency('Dev\Level')->on('SQL');
//$this->getDependency('Dev\Level')->on('DBSPEED');
class Liste extends Presenter{
	use Mixin_Pagination;
	protected $limitation				= 5;
	protected $truncation				= 369;
	protected $limitation2				= 10;
	protected $truncation2				= 60;
	protected $Query;
	function assign(){
		parent::assign();
		$this->taxonomy = lcfirst(end($this->presentNamespaces));
	}
	function dynamic(){
		parent::dynamic();
		//Registry::instance('Dev\Level')->MODEL();
		$uri = $this->URI;
		$this->page = $uri->page;
		$this->limit = $this->limitation;
		$this->offset = ($this->page!=NULL)?(($this->page-1)*$this->limitation):0;
		$this->subUri = (strrpos($uri[0],'s')===strlen($uri[0])-1?substr($uri[0],0,-1):$uri[0]);
		$Query = (new Query($this->taxonomy))
			->selectRelationnal([
				'user			<		email',
				'geopoint		>		label',
				'geopoint		>		lat',
				'geopoint		>		lon',
				'geopoint		>		radius',
				'tag			<>		name',
				'tag:thematic	<>		name',
				
				//'tag<>			taxonomy<> name',
				//'tag<>			taxonomy<> taxonomy2:taxonomies<> name',
			])
		;
		
		//$Query->from('test')->where('test.evenement_id = '.$this->taxonomy.'.id')->select('test.name as testName')->groupBy('test.name');
		
		if(method_exists($this,'addSelect'))
			$this->addSelect($Query);
		
		$i = 0;
		$this->thematics = [];
		$Query->openHavingOr();
		$this->groupingByAnd = false;
		while($tag=$uri[$i+=1]){
			if(is_array($tag)){
				$this->groupingByAnd = true;
				$Query->openHavingAnd();
				foreach($tag as $subTag){
					$Query->joinWhere($this->prefix.'tag.name = ? ',[$subTag]);
					$this->thematics[] = $subTag;
				}
				$Query->closeHaving();
			}
			else{
				$Query->joinWhere($this->prefix.'tag.name = ? ',[$tag]);
				$this->thematics[] = $tag;
			}
		}
		$Query->closeHaving();
		
		$rad = null;
		$lat = null;
		$lon = null;
	if($uri->geo||($uri->lat&&$uri->lon)){/**/
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
->from($this->prefix.'geopoint')
					->groupBy($this->prefix.'geopoint.lat')
					->groupBy($this->prefix.'geopoint.lon')
					->groupBy($this->prefix.'geopoint.radius')
					->select('geodistance('.$this->prefix.'geopoint.lat,'.$this->prefix.'geopoint.lon,?,?) as distance',[$lat,$lon])
					->orderBy('distance')
					->sort('ASC')
				;
			if($rad)
				$Query
					->select('(geodistance('.$this->prefix.'geopoint.lat,'.$this->prefix.'geopoint.lon,?,?)+COALESCE('.$this->prefix.'geopoint.radius,0)) as distance2inc',[$lat,$lon])
					->select('(geodistance('.$this->prefix.'geopoint.lat,'.$this->prefix.'geopoint.lon,?,?)-COALESCE('.$this->prefix.'geopoint.radius,0)) as distance2touch',[$lat,$lon])
->from($this->prefix.'geopoint')
				;
		}
		if($uri->phonemic){
			$Query
				->whereFullText('document',$uri->phonemic,'french')
				->selectFullTextHighlightTruncated('presentation',$uri->phonemic,$this->truncation,'french')
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
		
		if($Query->getDB()->getType()=='pgsql8'){ //for PgSql8 (no need in >=PgSql9.3)
			$Query
				->groupBy('id')
				->groupBy('title')
				->groupBy('tel')
				->groupBy('url')
				->groupBy('created')
				->groupBy('presentation')
				->groupBy('"'.$this->prefix.'user"."id"')
				->groupBy('"'.$this->prefix.'user"."email"')
			;
		}
		
		if($uri->phonemic)
			$Query
				->groupBy('document')
				->groupBy('user_id')
				->groupBy('id')
		;
		
		if($this->thematics->count())
			$Query
				->select('COUNT(DISTINCT('.$this->prefix.'tag.name)) as count_tag_rank')
				->from('tag')
				//->where($this->prefix.'thematic.name IN ?',[$this->thematics->getArray()])
				->where($this->prefix.'tag.name IN :thematic')
				->set($this->prefix.'tag',$this->thematics->getArray())
				->groupBy($this->prefix.''.$this->taxonomy.'.id')
				->orderBy('count_tag_rank')
				->sort('DESC')
			;

		$Query
			->orderBy($this->prefix.''.$this->taxonomy.'.created')
			->sort('DESC')
		;

		if($rad){//exit($lat);
			list($minlon, $minlat, $maxlon, $maxlat) = Geocoding::getBoundingBox([$lat,$lon],$rad,Geocoding::getEarthRadius('km'));
			if($uri->proxima){
				$distance2 = 'touch';
				$Query
					->openWhereOr()
					->where($this->prefix.'geopoint.minlat BETWEEN ? AND ?',[$minlat,$maxlat])
					->where($this->prefix.'geopoint.minlon BETWEEN ? AND ?',[$minlon,$maxlon])
					->where($this->prefix.'geopoint.maxlat BETWEEN ? AND ?',[$minlat,$maxlat])
					->where($this->prefix.'geopoint.maxlon BETWEEN ? AND ?',[$minlon,$maxlon])
					->closeWhere()
				;
			}
			else{
				$distance2 = 'inc';
				$Query
#					->where($this->prefix.'geopoint.minlat>=? AND '.$this->prefix.'geopoint.maxlat<=? AND '.$this->prefix.'geopoint.minlon>=? AND '.$this->prefix.'geopoint.maxlon<=?',[$minlat,$maxlat,$minlon,$maxlon])
					->where('"'.$this->prefix.'geopoint"."minlat">=? AND "'.$this->prefix.'geopoint"."maxlat"<=? AND "'.$this->prefix.'geopoint"."minlon">=? AND "'.$this->prefix.'geopoint"."maxlon"<=?',[$minlat,$maxlat,$minlon,$maxlon])
				;
			}
			
			//mysql - so simple - non sql standard
			//$Query->joinWhere("distance2{$distance2} <= ?",[$rad]);
#var_dump($Query->getQuery());
			//pgsql - more complex - non sql standard
			$Query = (new Query($this->taxonomy))
				->with($this->prefix.'view AS ('.$Query.')',$Query->getParams())
				->select('*')
				->from('view')
				->where("distance2{$distance2} <= ?",[$rad])
				->limit($this->limit,$this->offset)
			;#var_dump($Query->getQuery());
		}
		else{
			$Query
				->limit($this->limit,$this->offset)
			;
		}
		
		$this->count = $Query->countMD();
#bg		$this->count = $Query->count4D();
		//$this->count = R::count($this->taxonomy);
		
		$this->pagination();
		$this->pagination->href = clone $this->URI;
		unset($this->pagination->href->page);
		$this->pagination->href = $this->pagination->href;
		
		$this->liste = $Query->tableMD();
		//$this->liste = R::findAll($this->taxonomy,' LIMIT '.$this->limitation.' OFFSET '.$this->offset);
		
		$this->countListe = count($this->liste);
		
		foreach($this->liste->keys() as $akey){
			$this->liste[$akey]['atitle']=htmlspecialchars($this->liste[$akey]['title'], ENT_COMPAT);
		}
		//exit($this->liste);

		//sub flux
		$subCategories = [
			'evenement'=>'Événement',
			'ressource'=>'Ressource',
			'projet'=>'Projet',
			'association'=>'Association',
			'annonce'=>'Annonce',
			'mediatheque'=>'Médiathèque'
		];
		unset($subCategories[$this->taxonomy]);
		$full = [];
		$full = array_merge($full,$this->thematics->getArray());
		if($uri->phonemic)
			$full[] = $uri->phonemic;
		if($uri->geo)
			$full[] = $uri->geo;
		$full = implode(' ',$full);
		$XQuery2 = [];
		$XQuery2P = [];
		foreach(array_keys($subCategories) as $cat){
			if(!$Query->tableExists($cat))
				continue;
			$Query2 = (new Query($cat))
				->select(['id','pg_class.relname AS table','title','created'])
				->limit($this->limitation2)
				->from('"pg_class"')
				->where('"'.$this->prefix.$cat.'".tableoid = pg_class.oid')
			;
			if($full)
				$Query2
					->whereFullText('document',$uri->phonemic,'french')
					->selectFullTextHighlightTruncated('presentation',$full,$this->truncation2,'french',['StartSel' => '"<b>"', 'StopSel' => '"</b>"'])
				;
			else
				$Query2->selectTruncation('presentation',$this->truncation2);
			$XQuery2[] = "($Query2)";
			$XQuery2P = array_merge($XQuery2P,$Query2->getParams());
		}
		if(!empty($XQuery2)){
			$this->liste2 = R::getAll(implode(' UNION ',$XQuery2),$XQuery2P);
			for ($l2=0;$l2<count($this->liste2);$l2++)
				$this->liste2[$l2]['table'] = $subCategories[substr($this->liste2[$l2]['table'],strlen($Query2->getPrefix()))];
			//exit($this->liste2);		
		}
		$this->h1 = $uri[0];
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}
	function imageByItem($item){
		return 'content/'.$this->taxonomy.'/'.$item->id.'/'.$this->URI->filterParam($item->title).'.png';
	}
}
