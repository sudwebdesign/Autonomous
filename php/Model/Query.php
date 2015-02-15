<?php namespace Model;
use RedBeanPHP\Database;#->getRow4D()
use BadMethodCallException;
class Query extends \Surikat\Model\Query{
	protected static $heuristic;
	function __call($f,$args){#var_dump('<h3>querylocal</h3>',$f,$args);
		//parent::__call($f,$args);
/**/
		if(strpos($f,'get')===0&&ctype_upper(substr($f,3,1))){
			if(!$this->table||$this->tableExists($this->table)){
				$params = $this->composer->getParams();
				if(!empty($args))
					$params = array_merge($params,$args);
				$sql = $this->composer->getQuery();
				list($sql,$params) = R::nestBinding($sql,$params);
				return $this->DataBase->$f($sql,$params);
			}
			return;
		}
		else{
			switch($f){
				case 'orderByFullTextRank':
				case 'selectFullTextRank':
				case 'selectFullTextHighlight':
				case 'selectFullTextHighlightTruncated':
				case 'whereFullText':
					array_unshift($args,$this);
					call_user_func_array([$this->writer,$f],$args);
					return $this;
				break;
				case 'limit':
					call_user_func_array([$this->composer,'limit'],$args);
					return $this;
				break;
				default:
					if(substr($f,-5)=='Array'){
						$array = true;
						$f = substr($f,0,-5);
					}
					else{
						$array = false;
					}
					if(method_exists($this->composer,$f)){
						$un = strpos($f,'un')===0&&ctype_upper(substr($f,2,1));
						if(method_exists($this,$m='composer'.ucfirst($f)))
							$args = call_user_func_array([$this,$m],$args);
						$sql = array_shift($args);
						if($array)
							$binds = array_shift($args);
						else
							$binds = $args;
						if($sql instanceof SQLComposerBase){
							if(is_array($binds))
								$binds = array_merge($sql->getParams(),$binds);
							else
								$binds = $sql->getParams();
							$sql = '('.$sql->getQuery().')';
						}
						$args = [$sql,$binds];
						if($un){
							if(is_array($args[1])&&empty($args[1]))
								$args[1] = null;
						}
						call_user_func_array([$this->composer,$f],$args);
						return $this;
					}
				break;
			}
		}
		throw new BadMethodCallException('Class "'.get_class($this).'": call to undefined method '.$f);

	}
	function rowMD(){
		return Query::explodeAgg($this->table());
	}
	function tableMD(){
		return Query::explodeAggTableMD(
		$this->table());
	}
	static function explodeAggTableMD($data){
		$table = [];
		if(is_array($data)||$data instanceof \ArrayAccess)
			foreach($data as $i=>$d){
				$id = isset($d['id'])?$d['id']:$i;
				$table[$id] = self::explodeAgg($d);
			}
		return $table;
	}	


	//public helpers api
	function ignoring($k,$ignore){
/*		if (strstr($ignore,'LEFT'))*/#var_dump('<h1>ignoring</h1>::: ',$k,$ignore);
		return isset($this->_ignore[$k])&&in_array($ignore,$this->_ignore[$k]);
	}
	function ignoreTable(){
		foreach(func_get_args() as $ignore)
			$this->_ignore['table'] = $ignore;
	}
	function ignoreColumn(){
		foreach(func_get_args() as $ignore)
			$this->_ignore['column'] = $ignore;
	}
	function ignoreFrom(){
		foreach(func_get_args() as $ignore)
			$this->_ignore['from'] = $ignore;
	}
	function ignoreSelect(){
		foreach(func_get_args() as $ignore)
			$this->_ignore['select'] = $ignore;
	}
	function ignoreJoin(){
		return call_user_func_array([$this,'ignoreFrom'],func_get_args());
	}
	function select(){
		if(!$this->ignoring('select',func_get_arg(0)))
			return $this->__call(__FUNCTION__,func_get_args());
	}
	function join(){
		if(!$this->ignoring('join',func_get_arg(0))){#var_dump('<h1>querylocal JOIN </h1>::: '.func_get_arg(0).'<br />');
			
			
			return $this->__call(__FUNCTION__,func_get_args(0));
		}
	}
	function joinWhere($w,$params=null){
		if(empty($w))
			return;
		$this->having($this->joinWhereSQL($w),$params);
		return $this;
	}
	function unJoinWhere($w){
		if(empty($w))
			return;
		$this->unHaving($this->joinWhereSQL($w));
		return $this;
	}
	protected function joinWhereSQL($w){
		if(empty($w))
			return;
		$hc = $this->writer->sumCaster;
		$hs = implode(' AND ',(array)$w);
		if($hc)
			$hs = '('.$hs.')'.$hc;
		return 'SUM('.$hs.')>0';
	}
	function heuristic($reload=null){ //todo mode frozen
		if(!$this->table)
			return;
		if(!isset(self::$heuristic[$this->table])||$reload){
			if(!isset(self::$listOfTables))
				self::$listOfTables = $this->DataBase->inspect();
			$tableL = strlen($this->table);
			$h = [];
			$h['fields'] = in_array($this->table,self::$listOfTables)?$this->listOfColumns($this->table,null,$reload):[];
			$h['shareds'] = [];
			$h['parents'] = [];
			$h['fieldsOwn'] = [];
			$h['owns'] = [];
			foreach(self::$listOfTables as $table) //shared
				if((strpos($table,'_')!==false&&((strpos($table,$this->table)===0&&$table=substr($table,$tableL+1))
					||((strrpos($table,$this->table)===strlen($table)-$tableL)&&($table=substr($table,0,($tableL+1)*-1)))))
				&&!$this->ignoring('table',$table)){
						$h['shareds'][] = $table;
						$h['fieldsShareds'][$table] = $this->listOfColumns($table,null,$reload);
				}
			foreach($h['fields'] as $field) //parent
				if(strrpos($field,'_id')===strlen($field)-3){
					$table = substr($field,0,-3);
					if(!$this->ignoring('table',$table))
						$h['parents'][] = $table;
				}
			foreach(self::$listOfTables as $table){ //own
				if(strpos($table,'_')===false&&$table!=$this->table){
					$h['fieldsOwn'][$table] = $this->listOfColumns($table,null,$reload);
					if(in_array($this->table.'_id',$h['fieldsOwn'][$table])&&!$this->ignoring('table',$table))
						$h['owns'][] = $table;
				}
			}
			
			if(isset($h['fields']))
				foreach(array_keys($h['fields']) as $i)
					if($this->ignoring('column',$h['fields'][$i]))
						unset($h['fields'][$i]);
			if(isset($h['fieldsOwn']))
				foreach(array_keys($h['fieldsOwn']) as $table)
					foreach(array_keys($h['fieldsOwn'][$table]) as $i)
						if($this->ignoring('column',$table.'.'.$h['fieldsOwn'][$table][$i]))
							unset($h['fieldsOwn'][$table][$i]);
			if(isset($h['fieldsShareds']))
				foreach(array_keys($h['fieldsShareds']) as $table)
					foreach(array_keys($h['fieldsShareds'][$table]) as $i)
						if($this->ignoring('column',$table.'.'.$h['fieldsShareds'][$table][$i]))
							unset($h['fieldsShareds'][$table][$i]);
						
			self::$heuristic[$this->table] = $h;
		}
		return self::$heuristic[$this->table];
	}
	function autoSelectJoin($reload=null){
		$q = $this->writer->quoteCharacter;
		$agg = $this->writer->agg;
		$aggc = $this->writer->aggCaster;
		$sep = $this->writer->separator;
		$cc = $this->writer->concatenator;
		extract($this->heuristic($reload));
		foreach($parents as $parent){
			foreach($this->listOfColumns($parent,null,$reload) as $col){
				$this->select($this->writer->autoWrapCol($q.$this->prefix.$parent.$q.'.'.$q.$col.$q,$parent,$col).' as '.$q.$parent.'<'.$col.$q);
				$this->groupBy($q.$this->prefix.$parent.$q.'.'.$q.$col.$q);
			}
			$this->join("{$q}{$this->prefix}{$parent}{$q} ON {$q}{$this->prefix}{$parent}{$q}.{$q}id{$q}={$q}{$this->prefix}{$this->table}{$q}.{$q}{$parent}_id{$q}");
#			$this->join("LEFT OUTER JOIN {$q}{$this->prefix}{$parent}{$q} ON {$q}{$this->prefix}{$parent}{$q}.{$q}id{$q}={$q}{$this->prefix}{$this->table}{$q}.{$q}{$parent}_id{$q}");
			$this->groupBy($q.$this->prefix.$parent.$q.'.'.$q.'id'.$q);
		}
		foreach($shareds as $share){
			foreach($fieldsShareds[$share] as $col)
				$this->select("{$agg}(".$this->writer->autoWrapCol("{$q}{$this->prefix}{$share}{$q}.{$q}{$col}{$q}",$share,$col)."{$aggc} {$sep} {$cc}) as {$q}{$share}<>{$col}{$q}");
			$rel = [$this->table,$share];
			sort($rel);
			$rel = implode('_',$rel);
			$this->join("{$q}{$this->prefix}{$rel}{$q} ON {$q}{$this->prefix}{$rel}{$q}.{$q}{$this->table}_id{$q}={$q}{$this->prefix}{$this->table}{$q}.{$q}id{$q}");
#			$this->join("LEFT OUTER JOIN {$q}{$this->prefix}{$rel}{$q} ON {$q}{$this->prefix}{$rel}{$q}.{$q}{$this->table}_id{$q}={$q}{$this->prefix}{$this->table}{$q}.{$q}id{$q}");
			$this->join("{$q}{$this->prefix}{$share}{$q} ON {$q}{$this->prefix}{$rel}{$q}.{$q}{$share}_id{$q}={$q}{$this->prefix}{$share}{$q}.{$q}id{$q}");
#			$this->join("LEFT OUTER JOIN {$q}{$this->prefix}{$share}{$q} ON {$q}{$this->prefix}{$rel}{$q}.{$q}{$share}_id{$q}={$q}{$this->prefix}{$share}{$q}.{$q}id{$q}");
		}
		foreach($owns as $own){
			foreach($fieldsOwn[$own] as $col){
				if(strrpos($col,'_id')!==strlen($col)-3)
					$this->select("{$agg}(COALESCE(".$this->writer->autoWrapCol("{$q}{$this->prefix}{$own}{$q}.{$q}{$col}{$q}",$own,$col)."{$aggc},''{$aggc}) {$sep} {$cc}) as {$q}{$own}>{$col}{$q}");
			}
			$this->join("{$q}{$this->prefix}{$own}{$q} ON {$q}{$this->prefix}{$own}{$q}.{$q}{$this->table}_id{$q}={$q}{$this->prefix}{$this->table}{$q}.{$q}id{$q}");
#			$this->join("LEFT OUTER JOIN {$q}{$this->prefix}{$own}{$q} ON {$q}{$this->prefix}{$own}{$q}.{$q}{$this->table}_id{$q}={$q}{$this->prefix}{$this->table}{$q}.{$q}id{$q}");
		}
		if(!(empty($parents)&&empty($shareds)&&empty($owns))){
			$this->groupBy($q.$this->prefix.$this->table.$q.'.'.$q.'id'.$q);
			foreach($fields as $field)
				$this->groupBy($q.$this->prefix.$this->table.$q.'.'.$q.$field.$q);
		}
	}
	function countMD(){
		$q='"';#var_dump($this->prefix.$this->table);exit;
		return $this
			->getClone()
			->unLimit()
			->unOffset()
			->groupBy($q.$this->prefix.$this->table.$q.'.'.$q.'id'.$q)
			->countAll()
		;
	}
	function count4D(){
		$queryCount = clone $this;
		$queryCount->autoSelectJoin();
		$queryCount->unSelect();
		$queryCount->select('id');
		return (int)(new Query())->select('COUNT(*)')->from('('.$queryCount->getQuery().') as TMP_count',$queryCount->getParams())->getCell();
	}
	function table4D(){
		$this->selectNeed();
		$this->autoSelectJoin();
		return $this->getAll4D();
	}
	function row4D($compo=[],$params=[]){
		$this->selectNeed();
		$this->autoSelectJoin();
		$this->limit(1);
		return $this->getRow4D();
	}
}
