<?php
control::dev(
	control::dev_default
	|control::dev_js
);
class view extends surikat\view{
	static $xDom = 'x-dom/';
	static function preHooks(){ #don't forget to call exit to avoid simple Template auto-Mapping when hook found
		parent::preHooks(); #automatics hooks, just /service/
		
	}
	static function postHooks(){ #don't forget to call exit to avoid 404 when hook found
		control::dev(false);
		if(strpos(static::$PATH,'/blog/')===0)
			exit(include('plugin/wordpress/index.php'));
		
	}
	static function compileDocument($TML){
		parent::compileDocument($TML); #register "present:" in tml templates & auto min when PROD
		

		//add here your jquery-style manipulation on dom before compile
		
	}
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
