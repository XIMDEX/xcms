<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname (__FILE__).'/../../'));
}


require_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');
//

class DiskUtils {

	private function __construct() {

	}

	static private function computeFolder($folder) {
		return is_null($folder) ? \App::getValue( 'AppRoot') : $folder;
	}

	static public function disk_total_space($unit='B', $directory=null) {
		$directory = self::computeFolder($directory);
		$bytes = disk_total_space($directory);
		return self::transformUnits($bytes, $unit);
	}

	static public function disk_free_space($unit='B', $directory=null) {
		$directory = self::computeFolder($directory);
		$bytes = disk_free_space($directory);
		return self::transformUnits($bytes, $unit);
	}

	static public function transform($target = NULL, $unit = "B") {
		if(empty($target) ) return 0;

		preg_match("/(\d*)(\w*)/",$target, $out);

		return self::transformToUnits($out[1], $out[2], $unit);
	}

	static public function transformUnits($bytes, $unit) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$count = array_search($unit, $units);
		if ($count === false) {
			XMD_Log::error('It is being tried to transform a value to a invalid units: '.$unit);
			return false;
		}
		if ($count === 0) return $bytes;
		$bytes = round($bytes / pow(1024, $count), 2);
		return $bytes;
	}

	static public function transformToUnits($target, $unit_from, $unit_to) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$ini = array_search($unit_from, $units);
		$end = array_search($unit_to, $units);
		$diff = $ini - $end;

		if ($ini === false || $end === false) {
			XMD_Log::error('It is being tried to transform a value to a invalids units');
			return false;
		}

		if ($diff === 0) return $target;
		if ($diff > 0 ) {
			return round($target * pow(1024, $diff), 2);
		}else {
			$diff = -$diff;
			return round($target / pow(1024, $diff), 2);
		}
	}
}

?>