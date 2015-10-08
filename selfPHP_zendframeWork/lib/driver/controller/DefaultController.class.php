<?php 
namespace lib\driver\controller;
class DefaultController implements \lib\driver\controller\Controller{
	private $vars = [];
	
	public function assign($key, $value) {
		$this->vars[$key] = $value;
	}
	
	public function display($template) {
		foreach ($this->vars as $k => $v) { $$k = $v; }
		$templateDir = \lib\config\Controller::$TEMPLATE_DIR;
		require APP_ROOT . '/' . $templateDir . '/' . $template;
	}
}
?>