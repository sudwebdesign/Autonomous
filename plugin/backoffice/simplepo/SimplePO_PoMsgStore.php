<?php
interface SimplePO_PoMsgStore {
  public function write( $msg, $isHeader );
	public function read();
}
?>