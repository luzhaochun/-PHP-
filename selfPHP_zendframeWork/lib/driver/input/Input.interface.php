<?php
namespace lib\driver\input;
interface Input {
	public function get($key, $defaultValue, $callback, $filterEmpty);
	public function set($key, $value);
}
?>