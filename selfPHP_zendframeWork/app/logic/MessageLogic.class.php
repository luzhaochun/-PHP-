<?php
namespace app\logic;

class MessageLogic {
	public $error = "";
	private $messageSender;
	
	public function __construct() {
		$this->messageSender = new \app\extend\FengdiMessage();
	}
	
	//发送验证码
	public function sendPhoneCode($mobile, $code) {
		$result = $this->messageSender->send($mobile, "【wifi营销精灵】您绑定账号的手机验证码是：" . $code . ",有效时间2分钟");
		$result == false && $this->error = $this->messageSender->error;
		return $result;
	}
	
	//产生验证码:默认长度6
	public function createCode() {
		return \lib\tool\GeneralTool::createRandStr(6);
	}
}