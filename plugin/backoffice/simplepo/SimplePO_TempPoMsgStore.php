<?php
class SimplePO_TempPoMsgStore implements SimplePO_PoMsgStore {
	private $msgs;
	function write($msg,$isHeader) {
		 $this->msgs[] = $msg;
	}
	function read() {
		return $this->msgs;
	}
}
?>