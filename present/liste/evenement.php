<?php namespace present\liste;
final class evenement extends \present\liste{
	function addSelect($Query){
		$Query->selectRelationnal([
			'date			>		start',
			'date			>		end',
		]);
	}	
}