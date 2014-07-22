<?php namespace present;
use view;
use model;
use control\str;
use surikat\control\ArrayObject;
class find extends \present\liste{
	function assign(){
		parent::assign();
		//$this->taxonomy = end($this->presentNamespaces);#'find';
		//$this->tables = array('annonce','association','evenement','mediatheque','projet','ressource');
		$this->tables = array('Annonce','Association','Événement','Médiathèque','Projet','Ressource');
		$this->foo = 'bar2rire';
	}
	function dynamic(){
		$this->POST();
		$this->GET();
		parent::dynamic();
		$this->title .= ' : '.$this->search;
		$this->test = 'ok';
		$this->time = 0;
	}
	function POST(){
		if(!count($this->presentNamespaces)>count(explode('\\',__CLASS__))||empty($_POST))
			return;
		$this->formPosted = true;
		$this->search = $_POST['search'];
		//$type = $this->taxonomy;
	}
	function GET(){
		//des fois il post et redirige 
		//test avec get (vide) Alors View::param pour remplir $this->search ;)
		//$type = $this->taxonomy;
		if ($this->search === NULL){#||empty($_POST)
			$terms ='';
			foreach(uri::param() as $p){
				if ($p!="search"){
					$terms .= $p.' ';
				}
			}
			$this->search = substr($terms,0,-1);#si commenté (ramene tout)
		}
	}
	protected function getParamsFromUri(){
		$this->page = uri::param('page');
		$this->uri = $this->URI;
		$this->keywords = array();
		$i = 0;
		foreach(explode(' ',$this->search) as $param){
			$this->keywords[] = $param;
			//$this->uri .= '|'.$param;
		}
		//$this->uri .= '|'.$this->search;
		//$this->subUri = (strrpos($this->URI,'s')===strlen($this->URI)-1?substr($this->URI,0,-1):$this->URI);
		$this->subUri = $this->URI;	
	}
	protected function liste(){
		$this->sqlQueryListe = array_merge($this->sqlQuery,array(
			'limit'=>$this->limit,
			'offset'=>$this->offset,
		));
		$this->liste = new ArrayObject();
		$this->count = new ArrayObject();
		$this->countListe=0;
		foreach($this->tables as $taxo){
			$taxonomy =	str::unaccent(str::tolower($taxo));
			$this->liste[$taxo] = new ArrayObject(model::table4D($taxonomy,$this->sqlQueryListe,$this->sqlParams()));
			$this->count[$taxo] = model::count4D($taxonomy,$this->sqlQuery,$this->sqlParams());
			$this->countListe += count($this->liste[$taxo]);	
		}
	}
	protected function findSrcImageItems(){
		$this->imgsItems=NULL;		
		foreach($this->tables as $taxonomy){;
			foreach($this->liste[$taxonomy] as $item){
				$imgFolder = 'content/'.str::unaccent(str::tolower($taxonomy)).'/'.$item->id.'/';
				$imgName = str_replace(' ','-',$item->title);
				$imgsItem = glob($imgFolder."{".$imgName.".*}", GLOB_BRACE);
				$this->imgsItems[$taxonomy][$item->id] = $imgsItem;
			}
		}
	}
	function sayHello(){
		return '<h3>Make with OOP Framwork :: Surikat</h3>';
	}
}