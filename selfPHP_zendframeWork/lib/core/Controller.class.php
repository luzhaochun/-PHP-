<?php
namespace lib\core;
class Controller {
	private $ControllerDriver;
	public $error;
	
	public function __construct() {
		$this->setDriver(new \lib\config\Driver::$CONTROLLER_DRIVER ());
	}
	
	private function setDriver(\lib\driver\controller\Controller $driver) {
		$this->ControllerDriver = $driver;
		return true;
	}
	
	public function assign($key, $value) {
		return $this->ControllerDriver->assign($key, $value);
	}
	
	public function display($template) {
		return $this->ControllerDriver->display($template);
	}
}