<?php namespace Tool;
use Core\FS;
class deleter{
	static function alls($conf){
		$conf = array_merge([
			'dir'=>'',
			'deletion'=>false,
		],$conf);
		extract($conf);
		$func = 'removeFile'.($deletion?'s':'');
		return self::$func($dir,function($removeFile){},function($removeFile){});
	}
	
	protected static $extensionRewrite = [
		'jpeg'=>'jpg',
	];
	static function formatFilename($name){
		$name = filter_var(str_replace([' ','_',',','?'],'-',$name),FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$e = strtolower(pathinfo($name,PATHINFO_EXTENSION));
		if(isset(static::$extensionRewrite[$e]))
			$name = substr($name,0,-1*strlen($e)).static::$extensionRewrite[$e];
		return $name;
	}
	static function removeFile($dir,$callback=null){
		if(isset($dir)){
			if(!is_dir($dir)){
				unlink($dir);
				if($callback&&is_callable($callback))
					call_user_func($callback,$dir);
				return true;
			}
		}
	}
	static function removeFiles($dir,$callback=null){	
		if(FS::rmdir($dir)){
			if($callback&&is_callable($callback))
				call_user_func($callback,$dir);
			return true;
		}
	}
}
