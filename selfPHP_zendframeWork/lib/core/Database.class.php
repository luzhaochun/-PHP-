<?php
namespace lib\core;
class Database {
	public static $DRIVER;
	
	public static function setDriver(\lib\driver\database\Database $driver) {
		self::$DRIVER = $driver;
		return true;
	}
	
	public static function connect() {
		return self::$DRIVER->connect();
	}
	
	public static function query($sql) {
		return self::$DRIVER->query($sql);
	}
	
	public static function queryOne($sql) {
		return self::$DRIVER->queryOne($sql);
	}
	
	public static function exec($sql) {
		return self::$DRIVER->exec($sql);
	}
	
	public static function getLastSql() {
		return self::$DRIVER->getLastSql();
	}
}