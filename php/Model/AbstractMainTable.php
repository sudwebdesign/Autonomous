<?php namespace Model;
use Core\Ruler;
use Route\Faceted;
abstract class AbstractMainTable extends Table{
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
		$this->modified = date('Y-m-d H:i:s');
	}
	function onCreate(){
		$this->created = date('Y-m-d H:i:s');
	}
	function onChanged(){
		
	}
}