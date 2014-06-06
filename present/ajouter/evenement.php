<?php namespace present\ajouter;
use model;
use model\R;
use control\dates;
use control\filter;
final class evenement extends \present\ajouter{
	static function exec(){

	}
	static function POST_Specifications($bean){
		self::POST_Dates($bean);
	}
	protected static $datetimeCombine = true;
	protected static function POST_Dates($bean){
		static $vars = array('date_start','date_end','time_start','time_end');
		if(!isset($_POST['xownDate']))
			return;
		foreach($vars as $k) $$k = isset($_POST['xownDate'][$k])?$_POST['xownDate'][$k]:null;
		dates::dp_to_date_fr($date_start);
		dates::dp_to_date_fr($date_end);
		$multi = is_array($date_start);
		$se = $multi?'[]':'';
		if(!dates::validate_date($date_start,true))
			$bean->error('xownDate[date_start]'.$se,'missing or invalid format');
		if(!dates::validate_time($time_start))
			$bean->error('xownDate[time_start]'.$se,'invalid format');
		if(isset($_POST['date_with_end'])){
			if(!dates::validate_date($date_end))
				$bean->error('xownDate[date_end]'.$se,'invalid format');
			if(!dates::validate_time($time_end))
				$bean->error('xownDate[time_end]'.$se,'invalid format');
		}		
		if($multi){
			foreach(array_keys($date_start) as $i){
				if(self::$datetimeCombine){
					$date = array(
						'start'=>$date_start[$i].' '.(isset($time_start[$i])?$time_start[$i]:'00:00:00'),
						'end'=>$date_end[$i].' '.(isset($time_end[$i])?$time_end[$i]:'00:00:00'),
					);
				}
				else{
					$date = array();
					foreach($vars as $k)
						$date[$k] = isset(${$k}[$i])?${$k}[$i]:null;
				}
				$bean->xownDate[] = R::newOne('date',$date);
			}
		}
		else{
			if(self::$datetimeCombine){
				$date = array(
					'start'=>$date_start[$i].' '.(isset($time_start[$i])?$time_start[$i]:'00:00:00'),
					'end'=>$date_end[$i].' '.(isset($time_end[$i])?$time_end[$i]:'00:00:00'),
				);
			}
			else{
				$date = array();
				foreach($vars as $k)
					$date[$k] = $$k;
			}
			$bean->xownDate[] = R::newOne('date',$date);
		}
	}
	
}
