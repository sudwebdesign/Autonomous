<?php
include('surikat/control.php');control::dev();

use surikat\model\R;
use surikat\control\Geocoder\Geocoder;
use surikat\control\Geocoder\HttpAdapter\CurlHttpAdapter;
use surikat\control\Geocoder\Provider\ChainProvider;
use surikat\control\Geocoder\Provider\FreeGeoIpProvider;
use surikat\control\Geocoder\Provider\HostIpProvider;
use surikat\control\Geocoder\Provider\GoogleMapsProvider;
use surikat\control\Geocoder\Provider\NominatimProvider;
use surikat\control\Geocoder\Provider\OpenStreetMapProvider;

$geocoder = new Geocoder();
$adapter  = new CurlHttpAdapter();
$chain    = new ChainProvider(array(
    new NominatimProvider($adapter),
    new OpenStreetMapProvider($adapter),
    new GoogleMapsProvider($adapter), //new GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
    new FreeGeoIpProvider($adapter),
    new HostIpProvider($adapter),
    
));
$geocoder->registerProvider($chain);

function geocodeToAddr($geocode,$keys=array(
		'streetNumber',
		'streetName',
		'cityDistrict',
		'city',
		'zipcode',
		'country',
		//'region',
		//'regionCode',
		//'countyCode',
		//'county',
)){
	$addr = '';
	foreach($keys as $k){
		$m = 'get'.ucfirst($k);
		$t = trim($geocode->$m());
		if($t)
			$addr .= $t.',';
	}
	$addr = rtrim($addr,',');
	return $addr;
}

?><form><input name="search" value="<?php echo htmlentities($_GET['search']);?>"><input type="submit"></form><?php

if(!isset($_GET['search']))
	return;

try {
	echo '<pre>';

	//$query = '10 rue Gambetta, Paris, France';
	//$query = 'Costa de Xurius, Andorra';
	//$query = 'Amélie les bains Palalda, France';
	//$query = 'Amélie les bains, France';
	$query = $_GET['search'];
	R::addDatabase('geonames',"pgsql:host=localhost;dbname=geonames",'postgres','postgres',false);
	R::selectDatabase('geonames');
	$geoname = R::findOne('geoname',' WHERE name = ?',array($query));
	if(!$geoname)
		return;

	var_export($geoname);
	exit;
	
    $geocode = $geocoder->geocode($query);
	echo $query."\r\n";
	if(!$geocode->getBounds()
		||!$geocode->getLongitude()
		||!$geocode->getLatitude()
	)
		$geocode = $geocoder->geocode(geocodeToAddr($geocode));
	echo geocodeToAddr($geocode)."\r\n";
	var_export($geocode);
	
	echo '</pre>';
} catch (Exception $e) {
    echo $e->getMessage();
}