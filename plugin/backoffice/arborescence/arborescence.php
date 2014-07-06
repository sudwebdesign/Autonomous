<?php namespace Surikat;
FS::mkdir(SURIKAT_DATA.'arborescence/');
$types_file = SURIKAT_DATA.'/arborescence/root.svar';
if(is_file($types_file)){
	$types = unserialize(file_get_contents($types_file));
	if(!is_array($types)){
		$types = array();
	}
}
else{
	$types = array();
}

$path = isset($_GET['path'])?$_GET['path']:'';
$type = '';
if(!empty($path)){
	$x = explode('/',$path);
	$type = array_shift($x);
	$path = implode('/',$x);
}
$utype = ucfirst($type);
if($type){
	$bean = R::dispense($type)->loadPath($path);	
	$children = array_sort($bean->{"own$utype"},'position');
	$this->set('children',$children);
	// dump($children);
	
	$values = $bean->getProperties();
	$item = array();
	foreach($bean->model() as $k=>$v){
		if(is_numeric($k)){
			$k = $v;
		}		
		$item[$k] = isset($values[$k])?$values[$k]:'';
	}
	$this->set('item',$item);
	
	$path_segments = explode('/',$path);
	if($path_segments[0]==''){
		array_shift($path_segments);
	}
	array_unshift($path_segments,$type);
	array_unshift($path_segments,'');
	$this->set('path_segments',$path_segments);
}
else{
	if(isset($_POST['action'])&&isset($_POST['base'])){
		$base = $_POST['base'];
		switch($_POST['action']){
			case 'create':
				if(!in_array($base,$types)){
					$types[] = $base;
					file_put_contents($types_file,serialize($types));
				}
			break;
			case 'remove':
				$typesTMP = $types;
				$types = array();
				foreach($typesTMP as $v){
					if($v!=$base){
						$types[] = $v;
					}
				}
				file_put_contents($types_file,serialize($types));
			break;
		}
	}
	$this->set('children',$types);
}
$this->set(array(
	'type'			=>$type,
	'path'			=>$path,
	'pathdir'		=>(empty($type)?'':'/').$path.(empty($path)?'':'/'),
));
?>