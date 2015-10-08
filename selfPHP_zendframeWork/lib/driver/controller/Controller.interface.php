<?php 
namespace lib\driver\controller;
interface Controller {
	public function assign($key, $value);
	public function display($template);
}
?>