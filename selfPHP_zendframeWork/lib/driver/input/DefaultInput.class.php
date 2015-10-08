<?php
namespace lib\driver\input;
class DefaultInput implements \lib\driver\input\Input {
	public static $REQUEST_TYPE_GET = 'get';
	public static $REQUEST_TYPE_POST = 'post';
	
	public function get($key, $defaultValue = null, $callback = null, $filterEmpty = true) {
		if ($key == '') return null;
		
		$key = explode('.', $key);
		if (count($key) == 1) {
			$key = $key[0];
			$value = isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : null);
		} else {
			if(strtolower($key[0]) == self::$REQUEST_TYPE_GET) {
				$tmpResult = $_GET;
				unset($key[0]);
			} else if (strtolower($key[0]) == self::$REQUEST_TYPE_POST) {
				$tmpResult = $_POST;
				unset($key[0]);
			} else {
				$tmpResult = $_REQUEST;
			}
			foreach ($key as $k => $v) {
				$tmpResult = isset($tmpResult[$v]) ? $tmpResult[$v] : null;
			}
			$value = !is_null($tmpResult) ? $tmpResult : null;
		}
		
		if ($filterEmpty && empty($value)) $value = !is_null($defaultValue) ? $defaultValue : null;
		
		$value = !is_null($value) && \lib\tool\GeneralTool::functionExist($callback) ? $callback($value) : $value;
		return $value;
	}
	
	public function set($key, $value) {
		if ($key == '' || is_object($value) || is_array($value)) {
			return false;
		}
		$key = explode('.', $key);
	
		if (count($key) == 1) {
			$_GET[$key] = $value;
		} else {
			$method = 'get';
			if(strtolower($key[0]) == self::$REQUEST_TYPE_GET) {
				$evalString = '$_GET';
				unset($key[0]);
			} else if(strtolower($key[0]) == self::$REQUEST_TYPE_POST) {
				$evalString = '$_POST';
				unset($key[0]);
			} else {
				$evalString = '$_GET';
			}
			
			foreach ($key as $v) {
				$evalString .= '[\'' . $v . '\']';
			}
				
			if (true === $value) {
				$evalString .= '=true;';
			} else if (false === $value) {
				$evalString .= '=false;';
			} else {
				$evalString .= '=\'' . $value . '\';';
			}
			eval($evalString);
		}
		return true;
	}
}
?>