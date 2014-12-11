<?php namespace Controller;
class Application extends \Surikat\Controller\Application{
	function filterParam($path){
		return $this->Router->filterParam($path);
	}
}
