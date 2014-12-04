<?php namespace Model;
use Tool\ruler;
abstract class ATable_Main extends Table{
	protected $minTitreLength = 8;
	protected $maxTitreLength = 250;
	protected static $columnDocumentFulltextLanguage = 'french';
	protected static $columnDocumentFulltext = array(
		'title							/A',
		'tag			<>	name		/A',
		'presentation					/B',
		'geopoint		>	label		/B',
		'user			<	email		/C',
	);
	function onValidate(){
		if(!ruler::minchar($this->title,$this->minTitreLength))
			$this->error('title','Le titre doit comporter minimum '.$this->minTitreLength.' caractères');
		elseif(!ruler::maxchar($this->titre,$this->maxTitreLength))
			$this->error('title','Le titre doit comporter maximum '.$this->maxTitreLength.' caractères');
		if($this->tel&&!ruler::tel($this->tel))
			$this->error('tel','Numéro de téléphone non valide');
		if($this->url&&!ruler::url($this->url))
			$this->error('url','Lien non valide');
		$this->presentationHtml = $this->presentation;
		$this->presentation = html_entity_decode(strip_tags($this->presentationHtml));
		$this->titleHref = $this->Controller->filterParam($this->title);
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