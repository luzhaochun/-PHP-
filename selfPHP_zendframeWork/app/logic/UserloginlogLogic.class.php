<?php
namespace app\logic;

class UserloginlogLogic{
	
	public static $LOGIN_TYPE_ONEKEY = 1;
	public static $LOGIN_TYPE_QQ = 2;
	public static $LOGIN_TYPE_WEIBO = 3;
	public static $LOGIN_TYPE_WECHAT = 4;
	public static $LOGIN_TYPE_ROCK = 5;
	public static $LOGIN_TYPE_APP = 6;
	public static $LOGIN_TYPE_MOBILE = 7;//and so on...
	
	public $error = "";
	
	public function recordLoginLog($loginType){
		if(empty($_SESSION['hotspotId']) || empty($loginType)) return false;
		$hotspot_id = $_SESSION['hotspotId'];
		//get mid by hotspotid
		$query = "select mid from tx_hotspot where id=$hotspot_id";
		$mid = \lib\core\Database::queryOne($query)['mid'];
		if(empty(mid)) return false;
		$query = "insert into tx_userloginlog(`mid`,`ip`,`mac`,`datetime`,`logintype`,`hotspot_id`) 
				  values($mid,'".$_SESSION['ip']."','".$_SESSION['mac']."',".time().",$loginType,$hotspot_id)";
		$result = \lib\core\Database::exec($query);
		if($result === false){
			$this->error="插入数据库错误";
			return false ;
		}else{
			return true;
		}
	}
}