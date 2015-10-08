<?php 
namespace lib\driver\database;
interface Database {
	public function connect();
	public function query($sql);
	public function queryOne($sql);
	public function exec($sql);
	public function getLastSql();
}
?>