<?php namespace present\ajouter;
use model;
use model\R;
use control\dates;
final class evenements extends \present\ajouter{
	static function exec(){
		parent::POST('evenements');
	}
	protected static function POST($bean){
		self::POST_Dates($bean);
		//exit(print('<pre>'.print_r($bean->getArray(),true)));
		R::store($bean);
	}
	
	protected static function POST_Dates($bean){
		static $vars = array('date_start','date_end','time_start','time_end');
		if(!isset($_POST['dates']))
			return;
		foreach($vars as $k) $$k = isset($_POST['dates'][$k])?$_POST['dates'][$k]:null;
		dates::dp_to_date($date_start);
		dates::dp_to_date($date_end);
		if(!dates::validate_date($date_start))
			$bean->error('date_start','invalid format');
		if(!dates::validate_date($date_end))
			$bean->error('date_end','invalid format');
		if(!dates::validate_time($time_start))
			$bean->error('time_start','invalid format');
		if(!dates::validate_time($time_end))
			$bean->error('time_end','invalid format');

		if(is_array($date_start)){
			foreach(array_keys($date_start) as $i){
				$date = array();
				foreach($vars as $k)
					$date[$k] = isset(${$k}[$i])?${$k}[$i]:null;
				$bean->sharedDates[] = $date;
			}
		}
		else{
			$date = array();
			foreach($vars as $k)
				$date[$k] = $$k;
			$bean->sharedDates[] = $date;
		}
	}
	
}
