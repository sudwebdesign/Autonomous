<?php namespace Present\Liste;
final class Evenement extends \Present\Liste{
	function addSelect($Query){
		$Query->selectRelationnal([
			'date			>		start',
			'date			>		end',
		]);
	}	
}