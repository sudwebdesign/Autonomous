<?php namespace Present\Ajouter;
use Model;
use Model\R;
use Tool\filter;
final class Association extends \Present\Ajouter{
	function POST_Specifications($bean){
		if(isset($_POST['raison'])){
			$bean->raison = strip_tags($_POST['raison']);
		}
	}
}