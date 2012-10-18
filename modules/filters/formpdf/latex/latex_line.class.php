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
 *  @version $Revision: 7825 $
 */



class Latex_line extends Latex {

	var $style;

	var $start_x;
	var $start_y;
	var $end_x;
	var $end_y;

	function Latex_box() {
	}

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y;
	}

	function setWidth($w){
		$this->width = $w;
	}

	function setHeight($w){
		$this->height = $w;
	}

	function setStyle($style) {
		$this->style = $style;
	}

	function add_line() {
		// It opens a box
//		$resultado  = "\\entorno{".$this->style["border-radius"] ."}{".$this->style["border-width"]."}{".$this->style["background-color"]."}{".$this->style["border-color"]."}\n";
		$resultado = '';
		if (!empty($this->class)) {
			$resultado = "\\" . $this->class . "\n";
		}

		$this->y += ($this->height / 2) + 3;
		$resultado .= "\\linea{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}\n";

		return $resultado;
	}
//
}

?>
