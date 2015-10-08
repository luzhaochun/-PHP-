<?php
namespace app\controller;

class OauthController extends \lib\core\Controller {
	private $qqConfig;
	
	public function __construct() {
		parent::__construct();
		$this->qqConfig = [
			"appid"		=> "101176182",
			"appkey"	=> " f170ea5f2322adbbb1127ded18fb2292",
			"callback"	=> "wifizn.com"
		];
		$this->sinaWeiboConfig =[
			"WB_AKEY"          => "3326994459",
			"WB_SKEY"          => "693007a6895275979d545165251f4e4c",
			"WB_CALLBACK_URL"  => "http://portal1.wifizn.com/oauth.php?method=sinaWeiboCallback"
		];
	}
	
	public function qqLogin() {
		$o_qq =\app\extend\QQOauth::getInstance($this->qqConfig);
		$o_qq->login();
	}
	
	public function qqCallback() {
		$o_qq =\app\extend\QQOauth::getInstance($this->qqConfig);
		$o_qq->callback();
		$o_qq->get_openid();
		$o_qq->get_user_info();
	}
	
	public function sinaWeiboLogin(){
		$o_sina = new \app\extend\SaeTOAuthV2($this->sinaWeiboConfig['WB_AKEY'], $this->sinaWeiboConfig['WB_SKEY']);
		$code_url = $o_sina->getAuthorizeURL($this->sinaWeiboConfig['WB_CALLBACK_URL']);
		header("location:$code_url");
	}
	
	public function sinaWeiboCallback(){
		$o_sina = new \app\extend\SaeTOAuthV2($this->sinaWeiboConfig['WB_AKEY'], $this->sinaWeiboConfig['WB_SKEY']);
		if(isset($_REQUEST['code'])) {
			$keys = [];
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $this->sinaWeiboConfig['WB_CALLBACK_URL'];
			try {
				$token = $o_sina->getAccessToken('code',$keys);
			} catch (\app\extend\OAuthException $e) {
				
			}
		}
		if($token) {
			$_SESSION['token'] = $token;
			setcookie( 'weibojs_'.$o->client_id, http_build_query($token));
			
			$_SESSION['login'] = true;
			$userloginlogLogic = new \app\logic\UserloginlogLogic();
			$result = $userloginlogLogic->recordLoginLog($userloginlogLogic::$LOGIN_TYPE_WEIBO);
			if(!empty($result)){
				$loading_url  = SITE_URL."/index.php?method=loading";
				header("location:$loading_url");
			}
		}
	}
}