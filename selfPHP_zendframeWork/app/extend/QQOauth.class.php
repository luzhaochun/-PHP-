<?php
namespace app\extend;

class QQOauth {
	private static $_instance;
	private $config = array();
	
	private function __construct($config) {
		$this->Oauth_qq($config);
	}
	
	public static function getInstance($config) {
		if(!isset(self::$_instance)) {
			$c=__CLASS__;
			self::$_instance = new $c($config);
		}
		return self::$_instance;
	}
	
	private function Oauth_qq($config) {
		$this->config = $config;
		$_SESSION["appid"]    = $this->config['appid'];
		$_SESSION["appkey"]   = $this->config['appkey'];
		$_SESSION["callback"] = $this->config['callback'];
		$_SESSION["scope"] = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
	}
	
	function login() {
		$_SESSION['state'] = md5(uniqid(rand(), TRUE));
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
				. $_SESSION["appid"] . "&redirect_uri=" . urlencode($_SESSION["callback"])
				. "&state=" . $_SESSION['state']
				. "&scope=".$_SESSION["scope"];
		header("Location:$login_url");
	}
	
	function callback() {
		if($_REQUEST['state'] == $_SESSION['state']) {
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
					. "client_id=" . $_SESSION["appid"]. "&redirect_uri=" . urlencode($_SESSION["callback"])
					. "&client_secret=" . $_SESSION["appkey"]. "&code=" . $_REQUEST["code"];
	
			$response = get_url_contents($token_url);
			if (strpos($response, "callback") !== false) {
				$lpos = strpos($response, "(");
				$rpos = strrpos($response, ")");
				$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
				$msg = json_decode($response);
				if (isset($msg->error)) {
					echo "<h3>error:</h3>" . $msg->error;
					echo "<h3>msg  :</h3>" . $msg->error_description;
					exit;
				}
			}
	
			$params = array();
			parse_str($response, $params);
	
			$_SESSION["access_token"] = $params["access_token"];
		} else {
			echo("The state does not match. You may be a victim of CSRF.");
		}
	}
	
	function get_openid() {
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
				. $_SESSION['access_token'];
	
		$str  = get_url_contents($graph_url);
		if (strpos($str, "callback") !== false) {
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}
	
		$user = json_decode($str);
		if (isset($user->error)) {
			echo "<h3>error:</h3>" . $user->error;
			echo "<h3>msg  :</h3>" . $user->error_description;
			exit;
		}
	
		//set openid to session
		return $_SESSION["openid"] = $user->openid;
	}
	
	function get_user_info() {
		$get_user_info = "https://graph.qq.com/user/get_user_info?"
				. "access_token=" . $_SESSION['access_token']
				. "&oauth_consumer_key=" . $_SESSION["appid"]
				. "&openid=" . $_SESSION["openid"]
				. "&format=json";
	
		$info = get_url_contents($get_user_info);
		$arr = json_decode($info, true);
	
		return $arr;
	}
	
	public function __clone() {
		trigger_error('Clone is not allow' ,E_USER_ERROR);
	}
}
	
/* 公用函数 */
if (!function_exists("do_post")) {
	function do_post($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);

		curl_close($ch);
		return $ret;
	}
}

if (!function_exists("get_url_contents")) {
	function get_url_contents($url) {
		if (ini_get("allow_url_fopen") == "1")
			return file_get_contents($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result =  curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}