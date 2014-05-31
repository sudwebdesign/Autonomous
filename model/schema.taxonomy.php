<?php
$a = array();
foreach(array(
	'Évènement'=>array(
		'Salon',
		'Marché',
		'Vente directe',
		'Chantier collectif',
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
		'Vie Pratique',
		'Art et culture',
	),
) as $label=>$v){
	$a2 = array();
	foreach($v as $label2)
		$a2[] = array('type'=>'taxonomy','label'=>$label2);
	$a[] = array('type'=>'taxonomy','label'=>$label,'ownTaxonomy'=>$a2);
}
return $a;
