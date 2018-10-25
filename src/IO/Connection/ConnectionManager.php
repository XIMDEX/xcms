<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\IO\Connection;

use Ximdex\Logger;
use Ximdex\Models\Server;

class ConnectionManager
{
    // Static class emulation
	private static $baseName = 'Connection';
	private function ConnectionManager() {}
	
	static function getConnection($type, Server $server = null)
	{
		$baseFullPath = __DIR__.'/';
		$className = self::$baseName . self::normalizeName($type);
		$connectionclass = $baseFullPath.$className.'.php';
		$connection_routes = $baseFullPath . 'connection_routes.ini';
		if (!is_file($connectionclass)) {
			$fileRoutes = parse_ini_file($connection_routes);
			if (array_key_exists(strtolower($type), $fileRoutes)) {
				$tmpType = $type;
				$type = $fileRoutes[$type];
				if (!is_file($connectionclass)) {
					Logger::fatal("Connection $type neither $tmpType not implemented yet");
				}
			} else {
			    Logger::fatal("Connection $type not implemented yet");
			}
		}
		$factory = new \Ximdex\Utils\Factory($baseFullPath,self::$baseName);
		$conn = $factory->instantiate(self::normalizeName($type), $server, '\Ximdex\IO\Connection');
		$conn->setType($type);
		return $conn;
	}
	
	private static function normalizeName($name)
	{
		return ucfirst(strtolower($name));
	}
}
