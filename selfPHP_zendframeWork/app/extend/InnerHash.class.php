<?php
namespace app\extend;
class InnerHash {
	public static function HashToken() {
		return md5(time()/180 . "test");
	}
	
	public static function HashSession() {
		return md5(time()/180 . "session");
	}
}