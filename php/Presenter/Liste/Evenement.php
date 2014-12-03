<?php namespace Presenter\Liste;
final class Evenement extends \Presenter\Liste{
	function addSelect($Query){
		$Query->selectRelationnal([
			'date			>		start',
			'date			>		end',
		]);
	}	
}