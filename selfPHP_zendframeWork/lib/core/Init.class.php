<?php
namespace lib\core;
class Init {
	public static  $pdo = null;
	
	public static function run() {
		self::define();
		self::autoLoad();
		self::sessionStart();
		self::bootDriver();
		self::errSet();
		self::dbConnect();
	}
	
	public static function define() {
		define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
		define('SITE_ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
		define('APP_ROOT', dirname(dirname(dirname(__FILE__))));
		
		define('DOC_URL', 'http://portal1.wifizn.com');
		define('SITE_URL', 'http://192.168.2.243/projectWifi/php_portal_website/php_api');
		define('APP_URL', 'http://192.168.2.243/projectWifi/php_portal_website/php_api/app');
	}
	
	public static function errSet() {
		error_reporting(\lib\config\ErrorReport::$ERROR_LEVEL);
	}
	
	public static function autoLoad() {
		spl_autoload_register(function ($class) {
			$classFile = APP_ROOT . '/' . str_replace('\\', '/', $class) . '.class.php';
			$interfaceFile = APP_ROOT . '/' . str_replace('\\', '/', $class) . '.interface.php';
			
			if (file_exists($classFile)) {
				require $classFile;
			} else if (file_exists($interfaceFile)) {
				require $interfaceFile;
			} else {
				
			}
		});
	}
	
	public static function bootDriver() {
		\lib\core\Input::setDriver(new \lib\config\Driver::$INPUT_DRIVER ());
		\lib\core\Database::setDriver(new \lib\config\Driver::$DATABASE_DIRVER ());
		\lib\core\Router::setDriver(new \lib\config\Driver::$ROUTER_DRIVER ());
	}
	
	public static function sessionStart() {
		if (\lib\config\Session::$SESSION_START) session_start();
	}
	
	public static function dbConnect() {
		\lib\core\Database::connect();
	}
}