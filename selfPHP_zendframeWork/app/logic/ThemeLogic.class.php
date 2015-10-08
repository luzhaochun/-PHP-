<?php
namespace app\logic;

class ThemeLogic {
	public $error = "";
	
	public function getThemeByHotspotId($hotspotId) {
		$getThemeSql = "SELECT tt.*
			FROM tx_hotspot AS h  
			INNER JOIN tx_theme_mine AS ttm ON h.theme_id = ttm.id
			INNER JOIN tx_theme AS tt ON tt.id = ttm.theme_id
			WHERE h.id = $hotspotId";
		return \lib\core\Database::queryOne($getThemeSql);
	}
	
	public function getThemeByThemeId($themeId) {
		$getThemeSql = "SELECT * FROM tx_theme WHERE id = $themeId";
		return \lib\core\Database::queryOne($getThemeSql);
	}
}