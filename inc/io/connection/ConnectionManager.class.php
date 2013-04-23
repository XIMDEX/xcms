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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once (XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');

class ConnectionManager {
	private static $basePath = '/inc/io/connection/';
	private static $baseName = 'Connection_'; 
	// Emulacion de clase estática
	private function ConnectionManager() {}
	
	static function getConnection($type) {
		$baseFullPath = XIMDEX_ROOT_PATH . self::$basePath;
		$className = self::$baseName . self::normalizeName($type);
		$connectionclass = $baseFullPath.$className.'.class.php';
		$connection_routes = $baseFullPath . 'connection_routes.ini';


		if (!is_file( $connectionclass )) {

			$fileRoutes = parse_ini_file($connection_routes);

			if (array_key_exists(strtolower($type), $fileRoutes)) {
				$tmpType = $type;
				$type = $fileRoutes[$type];
				if (!is_file($connectionclass)) {
					XMD_Log::fatal("Connection $type neither $tmpType not implemented yet");
				}
			} else {
					XMD_Log::fatal("Connection $type not implemented yet");
				
			}
		}
		
		$factory = new Factory($baseFullPath,self::$baseName);

		return $factory->instantiate(self::normalizeName($type));
	}
	
	private static function normalizeName($name) {
		return ucfirst(strtolower($name));
	}
}

?>
