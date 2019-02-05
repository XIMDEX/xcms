<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Models\Server;
use Ximdex\Utils\Factory;

class ConnectionManager
{
    // Static class emulation
	private static $baseName = 'Connection';

	public static function getConnection(string $type, Server $server = null) : Connector
	{
		$baseFullPath = __DIR__ . '/';
		$className = self::$baseName . self::normalizeName($type);
		$connectionclass = $baseFullPath  .$className . '.php';
		$connection_routes = $baseFullPath . 'connection_routes.ini';
		if (! is_file($connectionclass)) {
			$fileRoutes = parse_ini_file($connection_routes);
			if (array_key_exists(strtolower($type), $fileRoutes)) {
				$tmpType = $type;
				$type = $fileRoutes[$type];
				if (! is_file($connectionclass)) {
				    throw new \Exception("Connection $type neither $tmpType not implemented yet");
				}
			} else {
			    throw new \Exception("Connection $type not implemented yet");
			}
		}
		$factory = new Factory($baseFullPath, self::$baseName);
		$conn = $factory->instantiate(self::normalizeName($type), [$server], '\Ximdex\IO\Connection');
		$conn->setType($type);
		return $conn;
	}
	
	private static function normalizeName(string $name) : string
	{
		return ucfirst(strtolower($name));
	}
}
