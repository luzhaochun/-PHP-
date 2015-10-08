<?php
namespace lib\core;
class Input {
	private static $driver;
	
	public static function setDriver(\lib\driver\input\Input $driver) {
		self::$driver = $driver;
		return true;
	}
	
	public static function get($key, $defaultValue = null, $callback = null, $filterEmpty = true) {
		return self::$driver->get($key, $defaultValue, $callback, $filterEmpty);
	}
	
	public static function set($key, $value) {
		return self::$driver->set($key, $value);
	}
}