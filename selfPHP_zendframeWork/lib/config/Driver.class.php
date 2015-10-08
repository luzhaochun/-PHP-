<?php 
namespace lib\config;
class Driver {
	public static $INPUT_DRIVER = '\lib\driver\input\DefaultInput';
	public static $DATABASE_DIRVER = '\lib\driver\database\mysql\Pdo';
	public static $CONTROLLER_DRIVER = '\lib\driver\controller\DefaultController';
	public static $ROUTER_DRIVER = '\lib\driver\router\DefaultRouter';
}
?>