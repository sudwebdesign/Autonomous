<?php
class SimplePO_DBConnection {
	private static $link;
	public static function getInstance() {
		if(!self::$link)
			self::$link = mysql_connect( X::Config('db_host'), X::Config('db_user'), X::Config('db_password') );
			mysql_select_db( X::Config('db_name') );
		return self::$link;
	}
	private function __construct() { }
	
}

class SimplePO_SQLException extends Exception {}
class SimplePO_Query {

	private $sql;
	private $cursor;
	private $link;
	private $table_prefix = 'i18n_';

	function __construct($link = null) {
		$this->sql = "";
		if(!$link) {
			$this->link = SimplePO_DBConnection::getInstance(); 
		}
	}
	function reset() {
		if($this->cursor) {
				@mysql_free_result($this->cursor);
				$this->cursor = null; 
		}
		$this->sql = "";
		return $this;
	}
	function sql() {
		if($this->cursor) {
				@mysql_free_result($this->cursor);
				$this->cursor = null;
		}
		$args = func_get_args();
		$sql = array_shift($args);
		// replace {} with table prefix
		$sql = preg_replace('/{([^}]*)}/',$this->table_prefix . '${1}',$sql);
		// escape arguments 
		$sql = str_replace('%','%%',$sql);
		$sql = str_replace('?','%s',$sql);
		$args = array_map(array('SimplePO_Query','escape'),$args);
		array_unshift($args,$sql);			
		$this->sql = call_user_func_array("sprintf",$args);
				
		return $this;
	}
	function appendSql() {
		if($this->cursor) {
			mysql_free_result($this->cursor);
			$this->cursor = null;
		}
		$args = func_get_args();
		$sql = array_shift($args);
		$sql = str_replace('?','%s',$sql);
		$args = array_map(array('SimplePO_Query','escape'),$args);
		array_unshift($args,$sql);			
		$this->sql .= call_user_func_array("sprintf",$args);
				
		return $this;
	
	}
	function fetchAll() {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		$res = array();
		while($row = mysql_fetch_assoc($this->cursor)) $res[] = $row;
		return $res;
	}
	function fetch() {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		return mysql_fetch_assoc($this->cursor);
	}
	function fetchOne() {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		$r = mysql_fetch_row($this->cursor);
		return $r[0];
	}
	function count() {
		if(!$this->cursor) {
				$this->makeCursor();
		}
		return mysql_num_rows($this->cursor);
	}
	function affectedRows() {
		if(!$this->cursor) {
				$this->makeCursor();
		}
		return mysql_affected_rows($this->link);
	}
	function insertId() {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		return mysql_insert_id($this->link);
	}
	function execute() {
		$this->makeCursor();
	}
	function fetchRow() {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		return mysql_fetch_row($this->cursor);
	}
	function fetchCol($index = 0) {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		$result = array();
		if(is_int($index)) {
			while($a = mysql_fetch_row($this->cursor)) $result[] = $a[$index];
		} else {
			while($a = mysql_fetch_assoc($this->cursor)) $result[] = $a[$index];
		}
		return $result;
	}
	function fetchAllKV() {
		if(!$this->cursor) {
			$this->makeCursor();
		}
		$res = array();
		while(list($a,$b) = mysql_fetch_row($this->cursor)) $r[$a] = $b;
		return $r;
	}
	protected function makeCursor() {
		$this->cursor = mysql_query($this->sql,$this->link);
		if($this->cursor === false) {
			$err = $this->getError();
			throw new SimplePO_SQLException("\n" . $err ."\n");
		}
	}
	function getError() {
		$err = "<pre>" . mysql_error() . "\n" . $this->sql . "</pre>";
		return $err;
	}
	function getSQL() {
		return $this->sql;
	}
	function getCursor() {
		return $this->cursor;
	}
	public function __toString() {
		return $this->sql;
	}
	public static function escape($value) {
		if (is_int($value) || is_float($value) ) {
			return "$value";
		} elseif(is_array($value)) {
			if(!empty($value)) {
				return implode(',',array_map(array('SimplePO_Query','escape'),$value));
			} else {
				return "NULL";
			}
		} elseif (is_null($value)) {
			return "''";
		} else {
			return "'" . mysql_real_escape_string($value) . "'";
		}
	}
}
?>