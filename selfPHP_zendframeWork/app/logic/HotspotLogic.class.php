<?php
namespace app\logic;

class HotspotLogic {
	public $error = "";
	
	public function getInfoById($hotspotId) {
		return \lib\core\Database::queryOne("SELECT * FROM tx_hotspot WHERE id = $hotspotId");
	}
	
	public function getHotspotIdByMac($mac) {
		$sql = "SELECT he.hotspot_id FROM tx_equipment AS e 
				INNER JOIN tx_hotspot_equipment AS he ON e.apsn = he.apsn 
				WHERE e.mac = '$mac'";
		$result = \lib\core\Database::queryOne($sql);
		return $result["hotspot_id"] ? $result["hotspot_id"] : 0;
	}
}