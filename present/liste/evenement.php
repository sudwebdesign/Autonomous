<?php namespace present\liste;
final class evenement extends \present\liste{
	function addSelect(){
		$this->Query->selectRelationnal([
			'date			>		start',
			'date			>		end',
		]);
	}	
}