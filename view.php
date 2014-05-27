<?php
control::dev(
	control::dev_default
	|control::dev_js
);
class view extends surikat\view{
	static $xDom = false;
	#<workflow>
	//static function compileDocument($TML){
		//parent::compileDocument($TML); #register P & auto min when PROD
	//}
	//static function index(){
		//self::hooks();
		//self::exec(self::param(0).'.tml');
	//}
	//static function getUriParams($path){
		//static $sepEq = ':';
		//static $sepAnd = '|';
		//static $sepOr = '&';
		//static $sepWord = '-';
		//$uriParams = array();
		//$min = array();
		//if(($pos=strpos($path,$sepEq))!==false)
			//$min[] = $pos;
		//if(($pos=strpos($path,$sepAnd))!==false)
			//$min[] = $pos;
		//if(!empty($min)){
			//$sepDir = min($min);
			//$uriParams[0] = substr($path,0,$sepDir);
			//$path = substr($path,$sepDir);
			//$x = explode($sepAnd,$path);
			//foreach($x as $v){
				//$x2 = explode($sepOr,$v);
				//if($k=$i=strpos($v,$sepEq)){
					//$k = substr($v,0,$i);
					//$v = substr($v,$i+1);
				//}
				//$v = strpos($v,$sepOr)?explode($sepOr,$v):$v;
				//if($k)
					//$uriParams[$k] = $v;
				//elseif(!empty($v))
					//$uriParams[] = $v;
			//}
		//}
		//else
			//$uriParams[0] = $path;
		//return $uriParams;
	//}
	#</workflow>
}
