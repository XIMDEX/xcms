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




class Latex_margin extends Latex {

	var $width;
	var $height;
	var $x;
	var $y;
	var $style;
	var $borderWidth;

	function Latex_margin() {
	}
	function setDim($width, $height) {
		$this->width = $width;
		if ((int)$height > 0) {
			$this->height = "-".$height;
		} else {
			$this->height = $height;
		}
	}
	function setBorderWidth($value) {
		$this->borderWidth = $value;
	}
	function add_margin() {
		//default values
/*
		if (empty($this->style["border-radius"])) {
			$this->style['border-radius'] = '0mm';
		}
		if (empty($this->style["border-width"])) {
			$this->style['border-width'] = '1mm';
		}
		if (empty($this->style["background-color"])) {
			$this->style['background-color'] = '1,1,1';
		}
		if (empty($this->style["border-color"])) {
			$this->style['border-color'] = '0,0,0';
		}*/
		$resultado = '';
		if (!empty($this->class)) {
			$resultado = "\\" . $this->class . "\n";
		}
//		$this->borderWidth = 1;
		if (!empty ($this->borderWidth)) {
			$correct = $this->borderWidth;
			$this->borderWidth *= 2;
			$this->x += $correct;
			$this->y += $correct;
		}
		// latex code to show the box
		$resultado .= "\\margen{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}{".$this->borderWidth."}\n";

		return $resultado;
	}
//
}

?>
