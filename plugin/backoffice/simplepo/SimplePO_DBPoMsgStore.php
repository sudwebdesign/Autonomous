<?php
class SimplePO_DBPoMsgStore implements SimplePO_PoMsgStore {
	public $update = false;
  public function init( $catalogue_name ){
    $q = new SimplePO_Query();
    $catalogue = $q->sql("SELECT * FROM {catalogues} WHERE name = ?",
 									$catalogue_name)->fetch();
    if (!$catalogue) {
      $q->sql("INSERT INTO {catalogues} (name) VALUES (?)", 
							$catalogue_name)->execute();
      $this->catalogue_id = $q->insertId();
    } else {
      $this->catalogue_id = $catalogue['id'];
    }
  }
  public function update( $msg, $isHeader ){
	$q = new SimplePO_Query();
	$msg['is_obsolete'] = !!$msg['is_obsolete'] ? 1 : 0;
	$msg['is_header'] = $isHeader ? 1 : 0;
	 if(!$isHeader){
		$messages = $q->sql("SELECT msgstr,comments as 'translator-comments' FROM {messages} WHERE catalogue_id=? AND msgid=?",$this->catalogue_id,@$msg["msgid"])->fetch();
		if(is_array($messages)){
			foreach($messages as $k=>$v){
				if(!empty($v)) $msg[$k] = $v;
			}
		}
	}
	$q->sql("DELETE FROM {messages} 
						WHERE  catalogue_id=? AND msgid=?",
						$this->catalogue_id,@$msg["msgid"])
						->execute();
    $q->sql("INSERT INTO {messages} 
						(catalogue_id, msgid, msgstr, comments, extracted_comments, reference,flags, is_obsolete, previous_untranslated_string,is_header)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)",$this->catalogue_id , @$msg["msgid"], @$msg["msgstr"], @$msg["translator-comments"], @$msg["extracted-comments"],
            @$msg["reference"], @$msg["flags"], @$msg["is_obsolete"], @$msg["previous-untranslated-string"],@$msg['is_header'])
						->execute();
  }
  public function write( $msg, $isHeader ){
	if($this->update) return $this->update($msg, $isHeader);
    $q = new SimplePO_Query();

		$msg['is_obsolete'] = !!$msg['is_obsolete'] ? 1 : 0;
		$msg['is_header'] = $isHeader ? 1 : 0;

    $q->sql("DELETE FROM {messages} 
						WHERE  catalogue_id=? AND BINARY msgid= BINARY ?",
						$this->catalogue_id,@$msg["msgid"])
						->execute();
    $q->sql("INSERT INTO {messages} 
						(catalogue_id, msgid, msgstr, comments, extracted_comments, reference,flags, is_obsolete, previous_untranslated_string,is_header)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)",$this->catalogue_id , @$msg["msgid"], @$msg["msgstr"], @$msg["translator-comments"], @$msg["extracted-comments"],
            @$msg["reference"], @$msg["flags"], @$msg["is_obsolete"], @$msg["previous-untranslated-string"],@$msg['is_header'])
						->execute();
  }

  public function read(){
    $q = new SimplePO_Query();
    return $q->sql("SELECT * FROM {messages} WHERE catalogue_id = ? ORDER BY is_header DESC,is_obsolete,id",
 										$this->catalogue_id)->fetchAll();
  }
}
?>