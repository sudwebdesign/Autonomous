<?php
/* echo SimplePO_JSON_RPC::Service(); */
class SimplePO_JSON_RPC {
	public static function Service(){
		return SimplePO_JSON_RPC::MessageService()->getResponse($_POST["request"]);
	}
	public static function env_fix(){
		if(get_magic_quotes_gpc()){
			$in = array(&$_GET, &$_POST, &$_COOKIE);
			while (list($k,$v) = each($in)) {
					foreach ($v as $key => $val) {
							if (!is_array($val)) {
									$in[$k][$key] = stripslashes($val);
									continue;
							}
							$in[] =& $in[$k][$key];
					}
			}
			unset($in);
		}
	}
	public static function MessageService(){
		self::env_fix();
		$rpc = new SimplePO_JSON_RPC(new SimplePO_MessageService());
		return $rpc;
	}
	protected $service;
	
	function __construct($obj) {
		$this->service = $obj;
	}
	function getResponse($request_string) {
		$request = json_decode($request_string,true);
		$response = array('error'=>null);
		
		if($request['id']){
			$response['id'] = $request['id'];
		}
		if(method_exists($this->service,$request['method'])) {
			try {
				// $r = call_user_method_array($request['method'],$this->service,$request['params']);
				$r = call_user_func_array(array($this->service, $request['method']), $request['params']);
				$response['result'] = $r;
			}
			catch (Exception $e) {
				$response['error'] = array('code' => -31000,'message' => $e->getMessage());
			}
		}
		else {
			$response['error'] = array('code' => -32601,'message' => 'Procedure not found.');
		}
		return json_encode($response);
	}
}
?>