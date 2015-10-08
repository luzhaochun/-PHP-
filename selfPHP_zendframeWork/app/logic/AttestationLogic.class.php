<?php
namespace app\logic;

class AttestationLogic {
	public $error = "";
	
	//免认证
	public static $AUTH_TYPE_NONE = 0;
	//一键认证
	public static $AUTH_TYPE_ONEKEY = 1;
	//账号登陆
	public static $AUTH_TYPE_ACCOUNT = 2;
	
	//通过hotspotId 获取用户设置的登陆方式
	public function getAttestationByHotspotId($hotspotId) {
		$sql = "SELECT * FROM tx_attestation WHERE id = $hotspotId";
		$userAttestationSettings = \lib\core\Database::queryOne($sql);
		
		//免认证
		if ($userAttestationSettings["type"] == self::$AUTH_TYPE_NONE) {
			return ["type" => $userAttestationSettings["type"], "detail" => []];
		//一键认证
		} elseif ($userAttestationSettings["type"] == self::$AUTH_TYPE_ONEKEY) {
			return ["type" => $userAttestationSettings["type"], "detail" => []];
		//账号登陆
		} else if ($userAttestationSettings["type"] == self::$AUTH_TYPE_ACCOUNT){
			$userSetAuth = json_decode($userAttestationSettings["detail"], 1);
			return ["type" => $userAttestationSettings["type"], "detail" => $userSetAuth];
		} else {
			$this->error = "不存在该种登陆方式";
			return false;
		}
	}
}