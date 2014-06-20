<?php namespace present\ajouter;
use model;
use model\R;
use control\filter;
final class association extends \present\ajouter{
	function POST_Specifications($bean){
		if(isset($_POST['raison'])){
			$bean->raison = strip_tags($_POST['raison']);
		}
	}
}
