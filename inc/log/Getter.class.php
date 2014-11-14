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




/**
 *
 */
class Getter {

	/**
	 * 
	 * @var unknown_type
	 */
	var $_params = null;
	/**
	 * 
	 * @var unknown_type
	 */
	var $_layout = null;

	/**
	 * Constructor
	 * @param $layout
	 * @param $params
	 * @return unknown_type
	 */
	function Getter($layout, $params) {
		$this->_layout = $layout;
		$this->_params = $params;
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function read() {
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function getLayout() {
		return $this->_layout;
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function getParams() {
		return $this->_params;
	}
}
?>