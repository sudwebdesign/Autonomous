<?php namespace Model;
use Core\Ruler;
use Core\Filter;
use Model\R;
use Model\Query;
use Route\Faceted;
use Tool\Dates;
use Tool\Geocoding;

abstract class ATableMain extends Table{
	protected $minTitreLength = 8;
	protected $maxTitreLength = 250;
	protected static $columnDocumentFulltextLanguage = 'french';
	protected static $columnDocumentFulltext = [
		'title							/A',
		'tag			<>	name		/A',
		'presentation					/B',
		'geopoint		>	label		/B',
		'user			<	email		/C',
	];
	function onValidate(){
		if(!Ruler::minchar($this->title,$this->minTitreLength))
			$this->error('title','Le titre doit comporter minimum '.$this->minTitreLength.' caractères');
		elseif(!Ruler::maxchar($this->titre,$this->maxTitreLength))
			$this->error('title','Le titre doit comporter maximum '.$this->maxTitreLength.' caractères');
		if($this->tel&&!Ruler::tel($this->tel))
			$this->error('tel','Numéro de téléphone non valide');
		if($this->url&&!Ruler::url($this->url))
			$this->error('url','Lien non valide');
		$this->presentationHtml = $this->presentation;
		$this->presentation = html_entity_decode(strip_tags($this->presentationHtml));
		$this->titleHref = (new Faceted())->filterParam($this->title);
	}
	function onUpdate(){
		$this->modified = @date('Y-m-d H:i:s');
	}
	function onCreate(){
		$this->created = @date('Y-m-d H:i:s');
	}
	function onChanged(){
		
	}
	
	static function CREATE($taxonomy,$data){
		$entry = R::create($taxonomy);
		
		//$user = isset($data['user'])?$data['user']:null;
		//if($user){
			//$user = R::findOrNewOne('user',array('email'=>$user));
			//$entry->user = $user;
		//}
		//else
			//$entry->error('user','required',true);
		
		$entry->title = strip_tags($data['title']);
		$entry->tel = $data['tel'];
		$entry->url = Filter::url($data['url']);
		$entry->presentation = Filter::strip_tags_basic($data['presentation']);
		if(isset($data['sharedTag'])&&isset($data['sharedTag']['name'])&&trim($data['sharedTag']['name'])){
			$max = 5;
			$tags = explode(' ',strip_tags($data['sharedTag']['name']));
			foreach($tags as $i=>$t){
				if($i>=$max)
					break;
				$t = (new Faceted())->filterParam($t);
				if(empty($t))
					continue;
				$tag = R::findOrNewOne('tag',$t);
				if($entry->user)
					$tag->sharedUser[] = $entry->user;
				$entry->sharedTag[] = $tag;
			}
		}
		if(isset($data['ownGeopoint'])){
			foreach($data['ownGeopoint'] as $g){
				if(isset($g['label'])&&$g['label']&&isset($g['lat'])&&$g['lat']!=''&&isset($g['lon'])&&$g['lon']!=''){
					$entry->xownGeopoint[] = R::create('geopoint',[
						'label' => $g['label'],
						'lat' => $g['lat'],
						'lon' => $g['lon'],
						'radius' => $g['radius'],
					]);
				}
			}
		}
		
		if(isset($data['xownDate'])){
			foreach($data['xownDate'] as $date){
				$date_start = isset($date['date_start'])?$date['date_start']:null;
				$date_end = isset($date['date_end'])?$date['date_end']:null;
				$time_start = isset($date['time_start'])?$date['time_start']:null;
				$time_end = isset($date['time_end'])?$date['time_end']:null;
				if($date_start){
					Dates::dp_to_date_fr($date_start);
					Dates::dp_to_date_fr($date_end);
					if(!Dates::validate_date($date_start,true))
						$entry->error('xownDate.date_start','missing or invalid format');
					if(!Dates::validate_time($time_start))
						$entry->error('xownDate.time_start','invalid format');
					if(isset($data['date_with_end'])){
						if(!Dates::validate_date($date_end))
							$entry->error('xownDate.date_end','invalid format');
						if(!Dates::validate_time($time_end))
							$entry->error('xownDate.time_end','invalid format');
					}
					$rdate = array(
						'start'=>$date_start?($date_start.' '.($time_start?$time_start:'00:00:00')):null,
						'end'=>$date_end?($date_end.' '.($time_end?$time_end:'00:00:00')):null,
					);
					if(!Dates::validate_datetime($rdate['start']))
						$entry->error('xownDate.date_start','missing or invalid format');
					if($rdate['end']&&!Dates::validate_datetime($rdate['end']))
						$entry->error('xownDate.date_end','missing or invalid format');
					$entry->xownDate[] = R::newOne('date',$rdate);
				}
			}
		}
		
		return $entry;
	}
	
	static function QUERY($taxonomy,$data=[]){
		$Query = new Query($taxonomy);
		$Query->selectRelationnal([		
			'geopoint		>		label',
			'geopoint		>		lat',
			'geopoint		>		lon',
			'geopoint		>		radius',
			
			'tag			<>		name',
		]);
		$Query->select([
			'id',
			'title',
			'titleHref',
			'tel',
			'url',
			'created'
		]);
		
		foreach($data as $k=>$v){
			switch($k){
				case 'tag':
					list($tags,$groupingByAnd) = $v;
					if(!empty($tags)){
						$Query->joinShared('tag');
						if($groupingByAnd){
							$Query->joinAdd('AND ('.self::tagRank($taxonomy).')=?',$tags,count($tags));
						}
						else{
							$Query->joinAdd('AND "{#prefix}tag"."name" IN ?',$tags);
						}
					}
				break;
				case 'geo':
					list($lat,$lon,$rad,$proxima) = $v;
					if($lat!==null&&$lon!==null&&$rad){
						$Query->join('"{#prefix}geopoint" ON "{#prefix}geopoint"."project_id" = "{#prefix}project"."id"');
						list($minlon, $minlat, $maxlon, $maxlat) = Geocoding::getBoundingBox([$lat,$lon],$rad,Geocoding::getEarthRadius('km'));
						if($proxima){
							$Query->openWhereOr();
						}
						else{
							$Query->openWhereAnd();
						}
						$Query
							->where('"{#prefix}geopoint"."minlat" BETWEEN ? AND ?',$minlat,$maxlat)
							->where('"{#prefix}geopoint"."minlon" BETWEEN ? AND ?',$minlon,$maxlon)
							->where('"{#prefix}geopoint"."maxlat" BETWEEN ? AND ?',$minlat,$maxlat)
							->where('"{#prefix}geopoint"."maxlon" BETWEEN ? AND ?',$minlon,$maxlon)
							->closeWhere()
							->where('(geodistance({#prefix}geopoint.lat,{#prefix}geopoint.lon,?,?)'
								.($proxima?'-':'+').'COALESCE({#prefix}geopoint.radius,0)) <= ?',$lat,$lon,$rad)
						;
					}
				break;
				case 'text':
					list($text,$lang,$truncation) = $v;
					if($text){
						$Query
							->whereFullText('document',$text,$lang)
							->selectFullTextHighlightTruncated('presentation',$text,$truncation,$lang,[
								'MaxFragments'=>2,
								'MaxWords'=>25,
								'MinWords'=>20,
								'ShortWord'=>3,
								'FragmentDelimiter'=>' ... ',
								'StartSel'=>'<b>',
								'StopSel'=>'</b>',
								'HighlightAll'=>'FALSE',
							])
						;
					}
					elseif($truncation){
						$Query->selectTruncation('presentation',$truncation);
					}
					else{
						$Query->select('presentation');
					}
				break;
				
				case 'orderByOrder':
					foreach($v as $by){
						switch($by){
							case 'geo':
								if($lat!==null&&$lon!==null){
									if(!$rad){
										$Query->join('"{#prefix}geopoint" ON "{#prefix}geopoint"."project_id" = "{#prefix}project"."id"');
									}
									$Query
										->orderBy('geodistance("{#prefix}geopoint"."lat","{#prefix}geopoint"."lon",?,?)',$lat,$lon)
										->sort('ASC')
									;
								}
							break;
							case 'text':
								if($text){
										$Query->orderByFullTextRank('document',$text,$lang);
								}
							break;
							case 'tag':
								if(!empty($tags)){
									$Query
										->orderBy('('.ATableMain::tagRank($taxonomy).')',$tags)
										->sort('DESC')
									;
								}
							break;
							case 'created':
								$Query
									->orderBy('{#prefix}'.$taxonomy.'.created')
									->sort('DESC')
								;
							break;
						}
					}
				break;
			}
		}
		
		return $Query;
	}
	static function tagRank($taxonomy){
		$Qt = new Query('tag');
		$relationShared = $Qt->relationShared($taxonomy);
		$Qt
			->select('COUNT("{#prefix}tag"."id")')
			->from('tag')
			->join('"{#prefix}'.$relationShared.'"')
			->joinOn('"{#prefix}'.$relationShared.'"."tag_id" = "{#prefix}tag"."id"
					AND "{#prefix}'.$relationShared.'"."'.$taxonomy.'_id" = "{#prefix}'.$taxonomy.'"."id"
					AND "{#prefix}tag"."name" IN ?')
		;
		return $Qt;
	}
}