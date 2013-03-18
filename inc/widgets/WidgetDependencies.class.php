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
 *  @version $Revision: 8529 $
 */




class WidgetDependencies {

	static protected $deps = null;
	static protected $parsed = null;

	private function __construct() {
	}

	protected static function loadDependencies() {
		if (self::$deps === null) {
			self::$deps = include(Config::getValue('AppRoot') . '/conf/wdeps.inc');
		}
		return self::$deps;
	}

	/**
	 * Returns an array of dependencies of $widget
	 * The "deps" attribute has dependencies in inclusion order.
	 */
	static public function getDependencies($widget) {

		self::$parsed = array();
		self::loadDependencies();
		$ret = self::_getDeps($widget);
		$ret = array_unique($ret);
		return $ret;
	}

	/**
	 * Look for dependencies of $widget recursivelly.
	 * Called initially from self::getDependencies()
	 */
	static protected function _getDeps($widget) {

		$jsFiles = array();
		if (!isset(self::$deps[$widget])) return $jsFiles;

		$jsFiles = self::$deps[$widget]['js'];
		for ($i=0, $l=count($jsFiles); $i<$l; $i++) {
			$jsFiles[$i] = $widget . '/js/' . $jsFiles[$i];
		}

		foreach (self::$deps[$widget]['deps'] as $wname) {
			if (!in_array($wname, self::$parsed)) {
				$jsFiles = array_merge(self::_getDeps($wname), $jsFiles);
				self::$parsed[] = $wname;
			}
		}

		return $jsFiles;
	}

}

?>
