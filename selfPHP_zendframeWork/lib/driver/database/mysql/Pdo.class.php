<?php 
namespace lib\driver\database\mysql;
class Pdo implements \lib\driver\database\Database {
	public static $pdo;
	private $lastSql;
	
	public function connect() {
		if (!self::$pdo) {
			self::$pdo = new \PDO("mysql:host=" 
					. \lib\config\Database::$HOST . ";dbname=" 
					. \lib\config\Database::$DATABASE_NAME,
				\lib\config\Database::$USER, \lib\config\Database::$PASSWORD);
		}
		self::query("set names " . \lib\config\Database::$ENCODE);
		return true;
	}
	
	public function query($sql) {
		$this->setLastSql($sql);
		$tmpRs = self::$pdo->query($sql);
		if (is_null($tmpRs) || empty($tmpRs)) { return [];}
		return $tmpRs->fetchAll();
	}
	
	public function queryOne($sql) {
		$this->setLastSql($sql);
		$result = self::$pdo->query($sql)->fetch();
		return $result ? $result : [];
	}
	
	public function exec($sql) {
		$this->setLastSql($sql);
		return self::$pdo->exec($sql);
	}
	
	private function setLastSql($sql) {
		$this->lastSql = $sql;
	}
	
	public function getLastSql() {
		return $this->lastSql;
	}
}
?>