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
class Getter_file extends Getter {
	
	function Getter_file($layout, $params) {
		parent::Getter($layout, $params);
	}

	function read() {
		
		$readedData = '';
		if (is_file($this->_params['file'])) {
			
			$fileSize = filesize($this->_params['file']);
			if ((int) $this->_params['quantity'] > 0) $quantity = (int) $this->_params['quantity'];
			$seekPosition = (int)(isset($quantity) && 
							is_int($quantity) && 
							(int) $quantity > 0 ? $fileSize - (int) $quantity : 0);
			
			if ($seekPosition > 0) {
				$handler = fopen($this->_params['file'], 'r');
				fseek($handler, $seekPosition - 1, 0);
				$readedData = fread($handler, $quantity);
				fclose($handler);
			} else {
				$readedData = file_get_contents($this->_params['file']);
			}
			
		}
		
		//echo nl2br(htmlentities($readedData));
		return $readedData;
	}

}

?>
