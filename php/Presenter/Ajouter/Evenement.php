<?php namespace Presenter\Ajouter;
use Model;
use Model\R;
use Tool\Dates;
use Core\Filter;
final class Evenement extends \Presenter\Ajouter{
	function POST_Specifications($bean){
		$this->POST_Dates($bean);
	}
	
	var $datetimeCombine = true;
	function POST_Dates($bean){
		static $vars = array('date_start','date_end','time_start','time_end');
		if(!isset($_POST['xownDate']))
			return;
		foreach($vars as $k) $$k = isset($_POST['xownDate'][$k])?$_POST['xownDate'][$k]:null;
		Dates::dp_to_date_fr($date_start);
		Dates::dp_to_date_fr($date_end);
		$multi = is_array($date_start);
		if(!Dates::validate_date($date_start,true))
			$bean->error('xownDate.date_start','missing or invalid format');
		if(!Dates::validate_time($time_start))
			$bean->error('xownDate.time_start','invalid format');
		if(isset($_POST['date_with_end'])){
			if(!Dates::validate_date($date_end))
				$bean->error('xownDate.date_end','invalid format');
			if(!Dates::validate_time($time_end))
				$bean->error('xownDate.time_end','invalid format');
		}
		if($multi){
			foreach(array_keys($date_start) as $i){
				if($this->datetimeCombine){
					$date = array(
						'start'=>@$date_start[$i]?$date_start[$i].' '.(@$time_start[$i]?$time_start[$i]:'00:00:00'):null,
						'end'=>@$date_end[$i]?$date_end[$i].' '.(@$time_end[$i]?$time_end[$i]:'00:00:00'):null,
					);
					if(!Dates::validate_datetime($date['start']))
						$bean->error('xownDate.date_start','missing or invalid format');
					if($date['end']&&!Dates::validate_datetime($date['end']))
						$bean->error('xownDate.date_end','missing or invalid format');
					//var_dump($date);exit;
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
			if($this->datetimeCombine){
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