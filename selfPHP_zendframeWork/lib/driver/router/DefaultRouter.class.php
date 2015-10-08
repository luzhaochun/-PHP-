<?php
namespace lib\driver\router;
class DefaultRouter implements \lib\driver\router\Router {
	
	//默认路由解析
	public function run($controllerName, $actionName) {
		$controllerName = "\app\controller\\" . ucfirst($controllerName) . "Controller";
		$controller = new $controllerName ();
		$controller->$actionName();
	}
}
?>