<?php
if(!@include(__DIR__.'/Surikat/Loader.php'))
	symlink('../Surikat','Surikat')&&include('Surikat/Loader.php');
use DependencyInjection\Registry;
use Model\ATableMain;
use Model\R;
use Validation\Ruler;
//use User\Session;
use HTTP/HTTP;
use Dev\LoremIpsum;

Registry::instance('Dev\Level')->PHP();
//Registry::instance('Dev\Level')->MODEL();
Registry::instance('Dev\Level')->DB();
//Registry::instance('Dev\Level')->DBSPEED();
//Registry::instance('Dev\Level')->SQL();
$it = pow(10,5);
set_time_limit(0);
HTTP::nocacheHeaders();
ob_implicit_flush(true);
//Session::start();
@ob_end_flush();
$LP = new LoremIpsum();
$LP->setWords(file(SURIKAT_PATH.'.data/liste.de.mots.francais.frgut.txt',FILE_IGNORE_NEW_LINES));
echo '<html><body></body></html>';
for($i=1;$i<=$it;$i++){
	$name = $LP->getText(25);
	while(!Ruler::minchar($name,4))
		$name = $LP->getText(6);
	ATableMain::CREATE('project',[
		'title' => trim($LP->getText(15)),
		//'presentation' => $LP->getHTML(250),
		'presentation' => $LP->getHTML(25),
		'sharedTag' => [
			'name' => $name
		],
		'ownGeopoint' => [[
			'label' => str_replace([',','.'],'',$LP->getText(1)),
			'lat' => rand(-90*1000,90*1000)/1000,
			'lon' => rand(-180*1000,180*1000)/1000,
			'radius' => rand(5*1000,60*1000)/1000,
		]],
		'url' => 'http://www.'.strtolower(str_replace([' ',',','--'],'-',$LP->getText(4))).'.com/',
		'tel' => rand(10000000000,99999999999),
	])->store();
	if($i===1)
		R::freeze(true);
	//echo "$i\n";
	echo '<script type="text/javascript">document.body.innerHTML = "'.$i.'";</script>';
}
