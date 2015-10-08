<?php
namespace lib\core;
class Router {
	private static $driver;
	
	public static function setDriver(\lib\driver\router\Router $driver) {
		self::$driver = $driver;
		return true;
	}
	
	public static function run($controllerName = "Index", $actionName = "index") {
		self::$driver->run($controllerName, $actionName);
	}
}