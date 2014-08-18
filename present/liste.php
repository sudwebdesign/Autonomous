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
		$uri->resolveMap([
			':int'=>function($param){
				//return R::load('taxonomy',$param);
				if(is_array($param)){
					$r = [];
					foreach($param as $p){
						if($t=R::load('tag',$p))
							$r[] = $t;
						else
							return false;
					}
					return $r;
				}
				else
					return R::load('tag',$param);
			},
			'geo',
			'phonemic'=>true,
		]);
		
		$this->Query = model::newFrom($this->taxonomy);
		$this->Query->selectRelationnal([
			'user			<		email',
			'date			>		start',
			'date			>		end',
			'tag			<>		name',
		]);

		$i = 0;
		$tagName = [];
		while($u=$uri[$i+=1])
			if(is_array($u)){
				foreach($u as $_u)
					$this->Query->joinWhere('tag.name IN ?',[[$_u]]);
			}
			else
				$tagName[] = $u;
		
		if(!empty($tagName))
			$this->Query->joinWhere('tag.name IN ?',[$tagName]);

		
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