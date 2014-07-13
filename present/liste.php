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
	
	protected $limitation				= 5;
	protected $finders = array(
		'taxonomyId',
		'localityId',
		'tagId',
		'texts',
	);
	
	function assign(){
		parent::assign();
		$this->taxonomy = end($this->presentNamespaces);
		
	}
	function dynamic(){
		parent::dynamic();
		
		$this->page = view::param('page');
		$this->uri = $this->URI;
		$this->subUri = (strrpos($this->URI,'s')===strlen($this->URI)-1?substr($this->URI,0,-1):$this->URI);

		$this->Query = model::newFrom4D($this->taxonomy);

		//findMotorParams
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
		$redirect = '';
		foreach($this->finders as $fr){
			if(!isset($this->assocParams[$fr]))
				continue;
			$this->assocParams[$fr]->sort(SORT_NATURAL|SORT_FLAG_CASE);
			$redirect .= implode('|',(array)$this->assocParams[$fr]).'|';
		}
		$redirect = trim($redirect,'|');
		if(trim(implode('|',(array)$this->keywords),'|')!=$redirect){
			header('Location: '.$this->HREF.'|'.$redirect,true,301);
			exit;
		}
		$this->keywords = array();
		$i = 0;
		while(($param = view::param($i+=1))!==null){
			$this->keywords[] = $param;
			$this->uri .= '|'.$param;
		}
		
		$this->find = array();
		//findMotorCompo
		foreach(array('taxonomy','locality','tag') as $t){
			$k = $t.'Id';
			if(!empty($this->find->$k))
				$this->Query->joinWhere($t.'.id IN ?',array((array)$this->find->$k));
		}
		
		foreach((array)$this->find->texts as $t){
			$this->fullText(array(
				'title',
				'presentation',
			),$t);
		}
		$this->Query->select(array('title','tel','url'));

		//$q = '"';
		//$this->Query->select($q.'locality'.$q.'.'.$q.'id'.$q.' as '.$q.'locality<>id'.$q);
		//$this->Query->select($q.'locality'.$q.'.'.$q.'label'.$q.' as '.$q.'locality<>label'.$q);
		//$this->Query->join('LEFT OUTER JOIN locality on geopoint.locality_id=locality.id');
		//$this->Query->group_by($q.'locality'.$q.'.'.$q.'id'.$q);
		
		$this->Query->selectTruncation('presentation',369);
		$this->Query->select('created');

		

		$this->count = $this->Query->count();
		
		$this->pagination();
		
		$this->liste = $this->Query->fork()->limit($this->limit)->offset($this->offset)->getAll();
		$this->countListe = count($this->liste);
		
		$this->findSrcImageItems();
		$this->h1 = view::param(0);
		if(!empty($this->keywords))
			$this->h1 .= ' - '.implode(' ',(array)$this->keywords);
		if($this->page>1)
			$this->h1 .= ' - page '.$this->page;
	}

	
	protected function findSrcImageItems(){
		$this->imgsItems=array();
		foreach($this->liste as $id=>$item){
			$imgFolder = 'content/'.substr(str::unaccent(str::tolower(view::param(0))),0,-1).'/'.$item->id.'/';
			$imgName = str_replace(' ','-',$item->title);
			$imgsItem = glob($imgFolder."{".$imgName.".*}", GLOB_BRACE);
			$this->imgsItems[$item->id] = $imgsItem;
		}
	}
}