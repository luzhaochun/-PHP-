<?php
namespace app\extend;
class FengdiMessage {
	private $host;
	private $name;
	private $password;
	private $planCode;
	private $extNum;
	private $preTime;
	private $level;
	
	private $error;
	
	private $errorCode;
	
	public function __construct() {
		$this->host = 'http://i2-sms.findia.cn/post/sms.ashx';
		$this->name = 'njtongxiang';
		$this->password = '123456';
		$this->planCode = 'ps179';
		$this->extNum = '';
		$this->preTime = '';
		$this->level = '';
		
		$this->errorCode = [
			'1000' => '没有获得IP鉴权',
			'1001A' => 'XMl错误',
			'1001B' => 'XMl错误',
			'1001C' => 'XMl错误',
			'1001D' => '非空参数为空',
			'1001E' => '参数不合规范',
			'1001F' => '参数不合规范',
			'1001G' => 'PreTime已过期',
			'1002A' => '用户不存在',
			'1002B' => '用户已冻结',
			'1002C' => '用户密码错误',
			'1003A' => '计划代码错误',
			'1003B' => '计划已停止',
			'1003C' => '计划非法（计划和用户不符）',
			'1003D' => '计划非法（计划和当前业务不符）',
			'1004' => '余额不足',
			'1005A' => '含不符合规范的手机号',
			'1005B' => '手机号数量不在规定范围内',
			'1005C' => '手机号数量不在规定范围内',
			'1006A' => '超过短信字数限制',
			'1007A' => '标题字数超长',
			'1007B' => '彩信报地址不是标准协议开头',
			'1007C' => '多网业务只支持图片',
			'9999' => '其他错误',
		];
	}
	
	public function __set($k, $v) {
		$this->$k = $v;
		return $this;
	}
	
	public function __get($k) {
		return $this->$k;
	}
	
	public function send($mobile, $content) {
		if (empty($mobile) || empty($content)) {
			$this->error = '手机号码或者发送内容不能为空';
			return false;
		}
		
		$postData = [
			'Name' => $this->name, 
			'Password' => $this->password, 
			'PlanCode' => $this->planCode, 
			'Mobile' => implode(',', $mobile),
			'Content' => $content,
		];
		if ($this->extNum) {
			$postData['ExtNum'] = $this->extNum;
		}
		if ($this->preTime) {
			$postData['PreTime'] = $this->preTime;
		}
		if ($this->level) {
			$postData['Level'] = $this->level;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->host);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		$output = curl_exec($ch);
		curl_close($ch);
		$output = explode(':', $output);
		if ($output[0] == 'success') {
			return true;
		} else {
			$this->error = $this->errorCode[$output[1]];
			return false;
		}
	}
}