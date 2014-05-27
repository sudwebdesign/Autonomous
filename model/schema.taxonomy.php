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
	'Ressources'=>array(
		'Compétence',
		'Ressource humaines',
		'Lieu',
		'Terrain',
		'Salle',
		'Outillage',
		'Véhicule',
	),
	'Projets'=>array(
		
	),
	'Annonces'=>array(
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
$clean = 'taxonomy';
return $a;
?>
