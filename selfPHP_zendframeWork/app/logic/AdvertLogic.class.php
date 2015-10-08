<?php
namespace app\logic;

class AdvertLogic {
	public $error = "";
	
	//引导页
	public static $POSITION_TYPE_1 = 1;
	//认证页
	public static $POSITION_TYPE_2 = 2;
	//等待页
	public static $POSITION_TYPE_3 = 3;
	
	//展示广告
	public static $ACTIVE_SHOW = 1;
	//点击点击广告
	public static $ACTIVE_CLICK = 2;
	
	public function getAdvertsByHotspotId($hotspotId, $position, $limit = 3) {
		$date = date("Y-m-d H:i:s");
		$sql = "SELECT * FROM tx_advert 
				WHERE hotspot_id = $hotspotId AND start_time < '$date' 
						AND end_time > '$date' AND position = $position AND status = 'true' ORDER BY id DESC LIMIT $limit";
		return \lib\core\Database::query($sql);
	}
	
	//展示一条广告并统计数据写入数据库uu
	public function showAdvertAndCollect($advertId, $hotspotId, $active) {
		if (!$this->testAdvertBelongHotspot($advertId, $hotspotId)) {
			$this->error = "该广告不属于该商户";
			return false;
		}
		
		if (!in_array($active, [self::$ACTIVE_CLICK, self::$ACTIVE_SHOW])) {
			$this->error = "不存在该事件";
			return false;
		}
		
		//更新广告列表记录
		$date = date("Y-m-d H:i:s");
		if ($active == self::$ACTIVE_CLICK) {
			$sql = "UPDATE tx_advert 
				SET click_count = click_count + 1 
				WHERE id = $advertId AND status = 'true' AND start_time < '$date' AND end_time > '$date'";
		} else {
			$sql = "UPDATE tx_advert
				SET show_count = show_count + 1 
				WHERE id = $advertId AND status = 'true' AND start_time < '$date' AND end_time > '$date'";
		}
		$result = \lib\core\Database::exec($sql);
		if ($result === false) {
			$this->error = "写入广告数据出错";
			return false;
		}
		
		//更新广告流水记录
		$createTime = date("Y-m-d H:i:s");
		$clientIP = \lib\tool\GeneralTool::getClientIp();
		$hotspotLogic = new \app\logic\HotspotLogic();
		$mid = $hotspotLogic->getInfoById($hotspotId)["mid"];
		if ($active == self::$ACTIVE_CLICK) {
			$sql = "INSERT INTO tx_advert_log 
					VALUES (NULL, $advertId, '$createTime', $hotspotId, $mid, 1, '$clientIP')";
		} else {
			$sql = "INSERT INTO tx_advert_log 
					VALUES (NULL, $advertId, '$createTime', $hotspotId, $mid, 0, '$clientIP')";
		}
		$result = \lib\core\Database::exec($sql);
		if ($result === false) {
			$this->error = "写入广告数据出错";
			return false;
		}
		
		//更新广告每日记录
		$date = date("Y-m-d");
		if ($active == self::$ACTIVE_CLICK) {
			$sql = "INSERT INTO tx_advert_daliy_log VALUES (NULL, $advertId, '$date', 0, 1) 
					ON DUPLICATE KEY UPDATE click_count = click_count + 1";
		} else {
			$sql = "INSERT INTO tx_advert_daliy_log VALUES (NULL, $advertId, '$date', 1, 0)
					ON DUPLICATE KEY UPDATE show_count = show_count + 1";
		}
		$result = \lib\core\Database::exec($sql);
		if ($result === false) {
			$this->error = "写入广告数据出错";
			return false;
		}
		return true;
	}
	
	//测试该用户是否属于该商户
	public function testAdvertBelongHotspot($advertId, $hotspotId) {
		$sql = "SELECT * FROM tx_advert WHERE id = $advertId AND hotspot_id = $hotspotId";
		return empty(\lib\core\Database::query($sql)) ? false : true;
	}
}