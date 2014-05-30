<?php namespace present\ajouter;
use model;
use model\R;
use control\dates;
use control\filter;
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
		$multi = is_array($date_start);
		$se = $multi?'[]':'';
		if(!dates::validate_date($date_start,true))
			$bean->error('dates[date_start]'.$se,'invalid format');
		if(!dates::validate_date($date_end))
			$bean->error('dates[date_end]'.$se,'invalid format');
		if(!dates::validate_time($time_start))
			$bean->error('dates[time_start]'.$se,'invalid format');
		if(!dates::validate_time($time_end))
			$bean->error('dates[time_end]'.$se,'invalid format');

		$_POST['presentation'] = filter::strip_tags_basic($_POST['presentation']);
		
		
		if($multi){
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
