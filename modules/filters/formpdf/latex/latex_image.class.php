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
 *  @version $Revision: 7740 $
 */



class Latex_image extends Latex {

	var $x;
	var $y;
	var $width;
	var $height;
	var $image_file;

	function Latex_image() {
	}

	function setPos($x, $y){
		$this->x = $x;
		$this->y = $y;
	}

	function setDim($width, $height){
		$this->width = $width;
		$this->height = $height;
	}

	function setImage($image){
		$this->image_file = $image;
	}

	function add_image(){
		$buffer = '';
		if (!empty($this->class)) {
			$buffer = "\\" . $this->class . "\n";
		}

		$buffer .= "\\imagen{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}{".$this->image_file ."}\n";
		return $buffer;
	}

}

?>
