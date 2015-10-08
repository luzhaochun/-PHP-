<?php
namespace app\controller;

class IndexController extends \lib\core\Controller {
	
	//没有主题时默认获取的主题
	public static $DEFAULT_THEME_ID = 1;
	
	public function index() {
		$gwmac = \lib\core\Input::get("gwmac", "AA-AA-3B-CC-DD-EF");
		$mac = \lib\core\Input::get("mac", "");
		$ip = \lib\core\Input::get("ip", "");
		$url = \lib\core\Input::get("url", "");
		$gwaddr = \lib\core\Input::get("gwaddr", "");
		$hotspotLogic = new \app\logic\HotspotLogic();
		$hotspotId = $hotspotLogic->getHotspotIdByMac($gwmac);
		
		if(!$hotspotId) {
			exit("找不到设备");
		}
		if (!$mac || !$ip || !$url || !$gwaddr) {
			exit("缺少路由参数");
		}
		
		//获取主题信息
		$themeLogic = new \app\logic\ThemeLogic();
		$themeInfo = $themeLogic->getThemeByHotspotId($hotspotId);
		$themeInfo = empty($themeInfo) ? $themeLogic->getThemeByThemeId(self::$DEFAULT_THEME_ID) : $themeInfo;
		
		//获取广告
		$advertLogic = new \app\logic\AdvertLogic();
		$adverts = $advertLogic->getAdvertsByHotspotId($hotspotId, \app\logic\AdvertLogic::$POSITION_TYPE_1);
		//路由器传入参数
		$_SESSION["gwmac"] = $gwmac;
		$_SESSION["mac"] = $mac;
		$_SESSION["ip"] = $ip;
		$_SESSION["url"] = $url;
		$_SESSION["gwaddr"] = $gwaddr;
		//解析生成参数
		$_SESSION['hotspotId'] = $hotspotId;
		$_SESSION['themeId'] = $themeInfo['id'];
		$_SESSION['title'] = $themeInfo['name'];

		//检查黑白名单
		$throughListLogic = new \app\logic\ThroughListLogic();
		$checkMac = $throughListLogic->check($hotspotId, $mac, \app\logic\ThroughListLogic::$TYPE_MAC);
		if($checkMac === false) {
			exit($throughListLogic->error);
		}
		$checkIp = $throughListLogic->check($hotspotId, $ip, \app\logic\ThroughListLogic::$TYPE_IP);
		if($checkIp === false) {
			exit($throughListLogic->error);
		}
		$checkIps = $throughListLogic->check($hotspotId, $ip, \app\logic\ThroughListLogic::$TYPE_IPS);
		if($checkIps === false) {
			exit($throughListLogic->error);
		}
		$checkDomain = $throughListLogic->check($hotspotId, $url, \app\logic\ThroughListLogic::$TYPE_DOMAIAN);
		if($checkDomain === false) {
			exit($throughListLogic->error);
		}
		var_dump($checkIps);
		//白名单 : 直接登陆
		if ($checkMac == 1 || $checkIp == 1 || $checkIps == 1 || $checkDomain == 1) {
			$_SESSION['login'] = true;
			header("location: ". SITE_URL . "/index.php?method=loading");
			exit;
		}
		//黑名单 : 不能登陆
		if ($checkMac == 0 || $checkIp == 0 || $checkIps == 0 || $checkDomain == 0) {
			exit("黑名单用户不能使用该路由器");
		}
		
		$attestationLogic = new \app\logic\AttestationLogic();
		$result = $attestationLogic->getAttestationByHotspotId($hotspotId);
		if ($result === false) {
			exit($attestationLogic->error);
		}
		//免认证,直接转到loading页面
		if ($result["type"] == 0) {
			$_SESSION['login'] = true;
			header("Location: ". SITE_URL . "/index.php?method=loading");exit;
		}
		
		$this->assign('title', $_SESSION['title']);
		$this->assign("adverts", $adverts);
		$this->display("theme_".$_SESSION['themeId']."/index.html");
	}
	
	public function login() {
		$advertLogic = new \app\logic\AdvertLogic();
		$hotspotId = $_SESSION['hotspotId'];
		$adverts = $advertLogic->getAdvertsByHotspotId($hotspotId, \app\logic\AdvertLogic::$POSITION_TYPE_2);
		$attestationLogic = new \app\logic\AttestationLogic();
		$result = $attestationLogic->getAttestationByHotspotId($hotspotId);
		if ($result === false) {
			exit($attestationLogic->error);
		}
		//一键上网,直接转到loading页面
		if ($result["type"] == 1) {
			$_SESSION['login'] = true;
			header("Location: ". SITE_URL . "/index.php?method=loading");exit;
		}
		//用户设置的登陆方式
		$this->assign('adverts', $adverts);
		$this->assign('title', !empty($_SESSION['title']) ? $_SESSION['title']:'');
		$this->display("theme_".(!empty($_SESSION['themeId'])? $_SESSION['themeId']:1)."/login.html");
	}
	
	public function home() {
		$this->checkLogin();
		$this->assign('title', $_SESSION['title']);
		$this->display("theme_".$_SESSION['themeId']."/home.html");
	}
	
	public function loading() {
		$this->checkLogin();
		$session = \app\extend\InnerHash::HashSession();
		$token = \app\extend\InnerHash::HashToken();
		//请求路由器
		$this->assign("gwaddr", $_SESSION["gwaddr"]);
		$this->assign("session", $session);
		$this->assign("token", $token);
		$this->assign('title', $_SESSION['title']);
		$this->display("theme_".$_SESSION['themeId']."/loading.html");
	}
	
	//ajax 发送短信验证信息
	public function sendValidateCode() {
		$mobile = \lib\core\Input::get("mobile", "");
		if($mobile == "") {
			echo json_encode(["status" => false, "msg" => "发送号码不能为空"]); exit;
		}
		
		if(!preg_match("/^(1[34578]\d{9})$/", $mobile)) {
			echo json_encode(["status" => false, "msg" => "发送号码格式不正确"]); exit;
		}
		
		$messageLogic = new \app\logic\MessageLogic();
		$sendCode = $messageLogic->createCode();
		$result = $messageLogic->sendPhoneCode([$mobile], $sendCode);
		if ($result == false) {
			echo json_encode(["status" => false, "msg" => $messageLogic->error]); exit;
		} else {
			//在session中设置验证码和有效期
			$_SESSION["validateCode"] = ["code" => $sendCode, "timeLine" => time()];
			echo json_encode(["status" => true, "msg" => "发送信息正确", "code" => $sendCode]);
		}
	}
	
	//ajax 验证
	public function ajaxValidateCode() {
		$code = \lib\core\Input::get("code");
		if ($code == "") {
			echo json_encode(["status" => false, "msg" => "验证码不能为空"]); exit;
		}
		$codeInfo = $_SESSION["validateCode"];
		
		if (!isset($codeInfo) || empty($codeInfo) || $codeInfo == null) {
			echo json_encode(["status" => false, "msg" => "验证失败"]); exit;
		}
		if (time() - $codeInfo["timeLine"] > 120) {
			echo json_encode(["status" => false, "msg" => "验证超时,请重新发送验证码"]);exit;
		}
		//SESSION['login'] = true 代表登陆成功
		if($codeInfo["code"] == $code) {
			$_SESSION['login'] = true;
			echo json_encode(["status" => true, "msg" => "验证成功"]); exit;
		} else {
			echo json_encode(["fasle" => false, "msg" => "验证不成功"]); exit;
		}
	}
	
	//ajax 统计广告展示次数
	public function ajaxCollectAdvert() {
		$hotspotId = $_SESSION["hotspotId"];
		$advertId = \lib\core\Input::get("advert_id", "");
		$active = \lib\core\Input::get("active", "");
		if (!$hotspotId) {
			echo json_encode(["status" => false, "msg" => "不存在店铺id"]); exit;
		}
		if (!$advertId) {
			echo json_encode(["status" => false, "msg" => "不存在广告id"]); exit;
		}
		if (!$active) {
			echo json_encode(["status" => false, "msg" => "不存在事件"]); exit;
		}
		$advertLogic = new \app\logic\AdvertLogic();
		$result = $advertLogic->showAdvertAndCollect($advertId, $hotspotId, $active);
		if ($result == false) {
			echo json_encode(["status" => false, "msg" => $advertLogic->error]); exit;
		}
		echo json_encode(["status" => true, "msg" => "广告统计成功"]); exit;
	}
	
	//判断是否登陆
	private function checkLogin() {
		return isset($_SESSION["login"]) && $_SESSION["login"] == true;
	}
}