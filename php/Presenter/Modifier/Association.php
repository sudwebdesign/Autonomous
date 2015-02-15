<?php namespace Presenter\Modifier;
use Model;
use Model\R;
use Tool\filter;
final class Association extends \Presenter\Modifier{
	function POST_Specifications($bean){
		if(isset($_POST['raison'])){
			$bean->raison = strip_tags($_POST['raison']);
		}
	}
}