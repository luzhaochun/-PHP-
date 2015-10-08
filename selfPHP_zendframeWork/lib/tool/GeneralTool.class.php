<?php
namespace lib\tool;
class GeneralTool {
	public static function functionExist($func) {
		return $func && ((is_string($func) && function_exists($func)) || get_class($func) == 'Closure');
	}
	
	//产生随机字符串
	public static function createRandStr($length) {
		$str = "abcdefghijklmnopqrstuvwxyz0123456789";
		for($i = 0; $i < $length; $i++) { $sendStr[$i] = $str{rand(1, strlen($str))}; }
		return implode("", $sendStr);
	}
	
	public static function getClientIp() {
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
			$ip = getenv("REMOTE_ADDR");
		} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else { $ip = "unknown"; }
		return $ip;
	}
}