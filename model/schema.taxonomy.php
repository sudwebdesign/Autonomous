<?php
$a = array();
foreach(array(
	'Événement'=>array(
		'Salon',
		'Marché',
		'Vente-directe',
		'Chantier-collectif',
		'Spectacle',
		'Animation',
		'Conférence',
		'Projection',
		'Débat',
		'Fête',
		'Actu',
	),
	'Ressource'=>array(
		'Compétence',
		'Bénévolat',
		'Lieu',
		'Terrain',
		'Salle',
		'Outillage',
		'Véhicule',
	),
	'Projet'=>array(
		
	),
	'Annonce'=>array(
		'Loisirs',
		'Alimentation',
		'Santé',
		'Logement',
		'Education',
		'Energie',
		'Transport',
		'Vie-Pratique',
		'Art-et-culture',
	),
	'Association'=>array(),
	'Médiathèque'=>array(),
) as $name=>$v){
	$a2 = array();
	foreach($v as $name2)
		$a2[] = array('type'=>'taxonomy','name'=>$name2);
	$a[] = array('type'=>'taxonomy','name'=>$name,'ownTaxonomy'=>$a2);
}
return $a;