<?php $sponsors = array(
	array(
		'Permaculture-et-Transition-en-Pays-catalan.jpg',
		'Permaculture transition en Pas Catalan',
		'http://www.transition.cat',
	),
	array(
		'sel-66.jpg',
		'safran della roma',
		'http://sel-66.jimdo.com/',
	),
	array(
		'sel-vallespir.jpg',
		'sel 66',
		'http://selvallespir.free.fr/',
	),
	array(
		'tous-des-createurs.jpg',
		'terre de liens',
		'http://tousdescreateurs66.over-blog.com/',
	),
	array(
		'alliance-sante.jpg ',
		'alliance-sante',
		'http://alliance-pour-la-sante.com/',
	),
	array(
		'habitat-et-humanisme.jpg',
		'habitat et humanisme',
		'http://www.habitat-humanisme.org/pyrenees-orientales/accueil',
	),
	array(
		'kokopelli-logo.jpg',
		'kokopelli',
		'http://kokopelli-semences.fr/',
	),
	array(
		'cravirola.gif',
		'cravirola',
		'http://www.cravirola.com/',
	),
	array(
		'nivet-galinier.jpg',
		'monnaie locale complémentaire',
		'http://nivet-galinier.over-blog.com/',
	),
	array(
		'floraluna.jpg',
		'floraluna',
		'http://www.floraluna.fr/',
	),
	array(
		'etre-et-devenir.jpg',
		'etre et devenir',
		'http://etreetdevenir.blogvie.com',
	),
	array(
		'Vallespir-en-Reseau.png',
		'Vallespir en Réseau',
		'http://vallespirenreseau.over-blog.com',
	),
	array(
		'astuces-maison.jpg',
		'astuces maison',
		'http://astuces.maison.over-blog.com/',
	),
	array(
		'sortir-du-nucleaire.jpg',
		'sel vallespir',
		'http://chernobyl-day.org/',
	),
	array(
		'couch-surfing.jpg',
		'couch surfing',
		'http://www.couchsurfing.org',
	),
	array(
		'echo-amo.jpg',
		'echo amo',
		'http://www.echoamo.info/spip/',
	),
	array(
		'universite-populaire.jpg',
		'universite populaire',
		'http://universitpopulaireperpignan.blogspot.fr/',
	),
	array(
		'cigales66.jpg',
		'cigales66',
		'http://cigales66.over-blog.com/',
	),
	array(
		'elephant-vert.png',
		'l\'elephant vert',
		'http://www.elephantvert.com',
	),
	array(
		'lire-et-faire-lire.jpg',
		'lire et faire lire',
		'http://www.lireetfairelire66.fr/',
	),
	array(
		'gfen.jpg',
		'gfen ',
		'http://gfen66.infini.fr/gfen66',
	),
	array(
		'terre-de-liens.jpg',
		'sortir du nucleaire',
		'http://www.terredeliens.org/',
	),
	array(
		'liberons-lenergie.jpg',
		'lecopot',
		'http://liberons-energie.fr/',
	),
	array(
		'adepopas.jpg',
		'adepopas',
		'https://pyrenees-terroir.com/products-page/agriculture-bio/la-pomme-de-terre-du-pays-de-sault/',
	),
	array(
		'bio-en-tet-logo_amap.png',
		'bio en tet',
		'http://www.bioentet.fr/',
	),
	array(
		'le-chant-de-leau.jpg',
		'laolu',
		'http://www.lechantdeleau.fr/index2.php',
	),
	array(
		'laolu-logo.jpg',
		'l\'aile universelle',
		'http://laolu-bouthic.blogspot.fr/',
	),
	array(
		'laile-universelle.jpg',
		'ligue de l\'enseignement',
		'http://aileuniverselle.free.fr/',
	),
	array(
		'zen-events.jpg',
		'zen events',
		'http://zen-events.kazeo.com/',
	),
	array(
		'energie-dici.png',
		'energie d\'ici',
		'http://www.energiesdici.com/',
	),
	array(
		'entraide-roussillon.jpg',
		'entraide rousillon',
		'http://www.entraidesroussillon.org/',
	),
	array(
		'pantherapie.png',
		'pantherapie',
		'http://www.pantherapie.fr/',
	),
	array(
		'resf.gif',
		'papiers plumes et poils',
		'http://resf66.free.fr',
	),
	array(
		'energie-citoyenne.jpg',
		'energie citoyenne',
		'http://energiecitoyenne.free.fr/',
	),
	array(
		'safran-della-roma.jpg',
		'resf',
		'http://www.safran-bio.fr/',
	),
	array(
		'relais.jpg',
		'relais',
		'http://www.relais-economiesolidaire66.org',
	),
	array(
		'artisans-du-monde.jpg',
		'Artisans du monde',
		'http://www.artisansdumonde.org/',
	),
	array(
		'la-maison-bleue.jpg',
		'kokopelli',
		'http://www.alamaisonbleue.org/',
	),
	array(
		'CriiEAU.jpg',
		'criieau',
		'',
	),
	array(
		'monnaie-locale-complementaire.jpg',
		'sel conflent',
		'http://monnaie-locale-complementaire.net/',
	),
	array(
		'logo-sel-2.png',
		'liberons l\'energie',
		'http://selduconflent.pagesperso-orange.fr/',
	),
	array(
		'lecopot.png',
		'le chant de l\'eau',
		'http://www.lecopot.fr/',
	),
);
foreach($sponsors as &$v)
	if(!isset($v[3]))
		$v[3] = $v[1];
shuffle($sponsors);
return $sponsors;
?>