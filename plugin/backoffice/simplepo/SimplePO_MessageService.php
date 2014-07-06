<?php
class SimplePO_MessageService {
	function __construct() {
	}
	function getMessages($id) {
      $q = new SimplePO_Query();
      $messages = $q->sql("SELECT * 
        FROM {messages} 
        WHERE catalogue_id=? AND is_header <> 1 
        ORDER BY msgstr != '', flags != 'fuzzy' ", $id)
        ->fetchAll();
			
			foreach($messages as &$m) {
				$m['fuzzy'] = strpos($m['flags'],'fuzzy') !== FALSE;
				$m['is_obsolete'] = !!$m['is_obsolete'];
			}
			return $messages;
	}
    function getCatalogues(){
      $q = new SimplePO_Query();
      return $q->sql("SELECT c.name,c.id,COUNT(*) as message_count, 
														 SUM(LENGTH(m.msgstr) >0) as translated_count
														 
											FROM {catalogues} c
											LEFT JOIN {messages} m ON m.catalogue_id=c.id
											GROUP BY c.id")->fetchAll();
    }
    function updateMessage($id, $comments, $msgstr, $fuzzy){
      $q = new SimplePO_Query();
			$flags = $fuzzy ? 'fuzzy' : '';
      $q->sql("UPDATE {messages} SET comments=?, msgstr=?, flags=? WHERE id=?", $comments, $msgstr, $flags, $id)->execute();
      // echo "true";
    }
	function makeError() {
		throw new Exception("This is an error");
	}
}
?>