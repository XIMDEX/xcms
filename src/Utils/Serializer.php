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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


namespace Ximdex\Utils;

use Ximdex\Logger;

if (!defined('SZR_JSON')) define('SZR_JSON', 'json');
if (!defined('SZR_XMLRPC')) define('SZR_XMLRPC', 'xmlrpc');

class Serializer
{
	static public function encode($mode, $var)
	{
		/**
		 * @var $instance SerializerJSON|SerializerXMLRPC
		 */
		$instance = Serializer::_factory($mode);
		$ret = $instance->encode($var);
		return $ret;
	}

	static public function decode($mode, $var)
	{
		/**
		 * @var $instance SerializerJSON|SerializerXMLRPC
		 */
		$instance = Serializer::_factory($mode);
		$ret = $instance->decode($var);
		return $ret;
	}

	static protected function _factory($mode)
	{

		// @todo Remove Namespace from class name
		$class = '\\Ximdex\\Utils\\Serializer' . strtoupper($mode);

		if ( class_exists( $class )) {
			$instance = new $class();
		} else  {
			Logger::error(sprintf("Serializer :: Class {%s} can not be instantiated.", $class));
			die;
		}
		return $instance;
	}

}
