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



class Serializer_JSON {

	protected $_json = null;

	public function __construct() {
		if (!function_exists('json_encode') || !function_exists('json_decode')) {
			require_once XIMDEX_ROOT_PATH . '/extensions/Services_JSON/Services_JSON.class.php';
			$this->_json = new Services_JSON();
		}
	}
	
	/**
	 * json_encode() / json_decode() needs an UTF-8 string
	 * When recoding a serialized string, the size of strings changes,
	 * so we need to hack the sizes of the serialized string.
	 * 
	 * See:
	 * 		http://es2.php.net/manual/en/function.serialize.php
	 * 		http://www.php.net/manual/es/function.unserialize.php#83997
	 */
	protected function recodeToUTF8($str) {
		
		$isArray = is_array($str);
		
		if ($isArray) {
			$str = serialize($str);
		}
		
		if (!XmlBase::isUtf8($str)) {
			$str = XmlBase::recodeSrc($str, XML::UTF8);
			// Corrects the values of the strings sizes
			$str = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $str);
//			$s2 = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $s2);
		}
		
		if ($isArray) {
			$str = unserialize($str);
		}
		
		return $str;
	}

	public function encode($var) {
		
		$var = $this->recodeToUTF8($var);
		
		if (is_null($this->_json)) {
			$ret = json_encode($var);
		} else {
			$ret = $this->_json->encode($var);
		}
		return $ret;
	}

	public function decode($var) {
		
		$var = $this->recodeToUTF8($var);
		
		if (is_null($this->_json)) {
			$ret = json_decode($var);
		} else {
			$ret = $this->_json->decode($var);
		}
		return $ret;
	}

}

?>