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




class debug {

	/**
	 * Serialize the PHP log
	 * @return unknown_type
	 */
	public static function log() {
		$args = func_get_args();
		$trace = debug::getTrace();
		foreach ($args as $key=>$data) {
			$args[$key] = debug::normalize($data);
			$args[$key] = print_r($args[$key], true);
		}
		$args[] = sprintf("on %s:%s [%s]\n", $trace['file'], $trace['line'], $trace['function']);
		$args = implode("\n\t", $args);
		error_log($args);
	}

	/**
	 * Serialize the browser
	 * @return unknown_type
	 */
	public static function dump() {
		$args = func_get_args();
		foreach ($args as $data) {
			$data = debug::normalize($data);
			echo '<pre>' . print_r($data, true) . '</pre>';
		}
	}

	protected static function normalize($data) {
		if ($data === null) $data = 'NULL';
		if ($data === false) $data = 'FALSE';
		if ($data === true) $data = 'TRUE';
		if (is_string($data) && strlen($data) == 0) $data = '(string) ""';
		return $data;
	}

	protected static function getTrace() {

		$trace = debug_backtrace(false);
		$t1 = $trace[1];
		$t2 = $trace[2];

		$ret = array(
			'file' => $t1['file'],
			'line' => $t1['line'],
			'function' => $t2['class'] . $t2['type'] . $t2['function']
		);

		return $ret;
	}
}
?>