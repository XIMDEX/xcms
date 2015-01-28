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



if (!defined('SZR_JSON')) define('SZR_JSON', 'json');
if (!defined('SZR_XMLRPC')) define('SZR_XMLRPC', 'xmlrpc');

class Serializer {

	private function __construct() {
	}

	static public function encode($mode, $var) {
		$instance =& Serializer::_factory($mode);
		$ret = $instance->encode($var);
		return $ret;
	}

	static public function decode($mode, $var) {
		$instance =& Serializer::_factory($mode);
		$ret = $instance->decode($var);
		return $ret;
	}

	static protected function & _factory($mode) {
		$class = 'Serializer_' . strtoupper($mode);
		$class_file = "{$class}.class.php";
		$class_path = "/inc/serializer/{$mode}/{$class_file}";
		ModulesManager::file($class_path);

		if (!class_exists($class)) {
			XMD_Log::error(sprintf(_("Serializer :: The class {%s} could not be instanced."), $class));
			die;
		}
		$instance = new $class();
		return $instance;
	}

}

?>