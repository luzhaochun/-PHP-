<?php
namespace app\logic;
class ThroughListLogic {
	public $error;
	
	//1:手机 2:mac地址 3:单ip 4:ip段 5:域名 6:端口
	public static $TYPE_PHONE = 1;
	public static $TYPE_MAC = 2;
	public static $TYPE_IP = 3;
	public static $TYPE_IPS = 4;
	public static $TYPE_DOMAIAN = 5;
	public static $TYPE_PORT = 6;
	
	public static $REGEX_PHONE = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/";
	public static $REGEX_MAC = "/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i";
	public static $REGEX_DOMAIN = "/^[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?$/";
	public static $REGEX_PORT = "/^[0-9]{1,5}$/";
	
	// result === 0 不放通  | result === 1 放通 | result === false 错误 | result === -1 不存在该值的记录,正常登陆
	public function check($hotspotId, $value, $type) {
		if ($type == self::$REGEX_PHONE) {
			if (!preg_match(self::$REGEX_PHONE, $value)) {
				$this->error = "手机号码格式不正确";
				return false;
			}
			$sql = "SELECT * FROM tx_hotspot_through_list WHERE hotspot_id = $hotspotId AND type = $type AND value = '$value'";
			$result = \lib\core\Database::queryOne($sql);
			if (empty($result)) {
				return -1;
			}
			return $result["through"];
		} else if ($type == self::$TYPE_MAC) {
			if (!preg_match(self::$REGEX_MAC, $value)) {
				$this->error = "MAC地址格式不正确";
				return false;
			}
			$sql = "SELECT * FROM tx_hotspot_through_list WHERE hotspot_id = $hotspotId AND type = $type AND value = '$value'";
			$result = \lib\core\Database::queryOne($sql);
			if (empty($result)) {
				return -1;
			}
			return $result["through"];
		} else if ($type == self::$TYPE_IP) {
			$longIp = ip2long($value);
			if ($longIp == -1 || $longIp === false) {
				$this->error = 'ip地址格式不正确';
				return false;
			}
			$sql = "SELECT * FROM tx_hotspot_through_list WHERE hotspot_id = $hotspotId AND type = $type AND value = '$value'";
			$result = \lib\core\Database::queryOne($sql);
			if (empty($result)) {
				return -1;
			}
			return $result["through"];
		} else if ($type == self::$TYPE_IPS) {
		$longIp = ip2long($value);
			if ($longIp == -1 || $longIp === false) {
				$this->error = 'ip地址格式不正确';
				return false;
			}
			$sql = "SELECT * FROM tx_hotspot_through_list WHERE hotspot_id = $hotspotId AND `type` = $type 
				AND INET_ATON(SUBSTRING_INDEX(`value`, '-', 1)) <= INET_ATON('$value') AND INET_ATON(SUBSTRING_INDEX(`value`, '-', -1)) >= INET_ATON('$value')";
			$result = \lib\core\Database::queryOne($sql);
			if (empty($result)) {
				return -1;
			}
			return $result["through"];
		} else if ($type == self::$TYPE_DOMAIAN) {
			if (!preg_match(self::$REGEX_DOMAIN, $value)) {
				$this->error = "域名格式不正确";
				return false;
			}
			$sql = "SELECT * FROM tx_hotspot_through_list WHERE hotspot_id = $hotspotId AND type = $type AND value = '$value'";
			$result = \lib\core\Database::queryOne($sql);
			if (empty($result)) {
				return -1;
			}
			return $result["through"];
		} else if ($type == self::$TYPE_PORT) {
			if (!preg_match(self::$REGEX_PORT, $value)) {
				$this->error = "端口格式不正确";
				return false;
			}
			$sql = "SELECT * FROM tx_hotspot_through_list WHERE hotspot_id = $hotspotId AND type = $type AND value = '$value'";
			$result = \lib\core\Database::queryOne($sql);
			if (empty($result)) {
				return -1;
			}
			return $result["through"];
		} else {
			$this->error = "登陆类型错误";
			return false;
		}
	}
}