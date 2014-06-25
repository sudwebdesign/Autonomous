<?php
namespace surikat\control\Geocoder{
	include '../surikat/control.php';
	\control::dev();
	$geocoder = new Geocoder();
	$adapter  = new HttpAdapter\CurlHttpAdapter();
	$chain    = new Provider\ChainProvider(array(
		new Provider\FreeGeoIpProvider($adapter),
		new Provider\HostIpProvider($adapter),
		new Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
	));
	$geocoder->registerProvider($chain);

	try {
		$geocode = $geocoder->geocode('10 rue Gambetta, Paris, France');
		//$geocode = $geocoder->geocode('88.188.221.14');
		var_export($geocode);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}
