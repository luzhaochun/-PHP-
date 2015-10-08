<?php
namespace lib\driver\router;
interface Router {
	public function run($controllerName, $actionName);
}
?>