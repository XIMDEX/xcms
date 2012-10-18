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




class Latex_listing extends Latex {

	var $items;
	var $style;
	var $x;
	var $y;
	var $width;
	var $height;
	var $type;

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y;
	}

	function setDim($width,$height){
		$this->width = $width;
		$this->height = $height;
	}

	function setStyle($style){
		$this->style = $style;
	}

	function setItems($items){
		$this->items = $items;
	}

	function setType($type){
		$this->type = $type;
	}

	function _createList($elements) {
		if($this->type == "unordered"){
			$buffer = "\\begin{itemize}\n";
		} else {
			$buffer = "\\begin{enumerate}\n";
		}

		$buffer .= $this->_recursiveList ($elements);
//		$buffer = "\item\colorbox{background}{".$buffer."}";

		if($this->type == "unordered"){
			$buffer .= "\\end{itemize}\n";
		} else {
			$buffer .= "\\end{enumerate}\n";
		}

		return $buffer;
	}

	function _recursiveList ($elements) {
		reset ($elements);
		$buffer="";
		while (list (, $element) = each ($elements)) {
			if (is_string ($element)) {
				$buffer .= "\item ".$this->filterSpecialChars($element)."\n";
			} elseif (is_array ($element)) {
				$buffer .= $this->_recursiveList ($element);
			}
		}

		return $buffer;
	}

	function add_list() {
		$background_color = "1,1,1";
   		$border_color = "1,1,1";
       	$border_size = "0";
		$buffer = "";

		$buffer = $this->_createList($this->items);
		$resultado = '';
		if (!empty($this->class)) {
			$resultado = "\\" . $this->class . "\n";
		}
		$this->y -= 3;
		$resultado .= "\\entornoLista{".$this->width."}{".$background_color."}{".$border_color."}{".$border_size."}\n";
         	$resultado .= "\\lista{".$this->x."}{".$this->y."}{".$this->width."}{".$this->height."}{".$buffer."}\n";

		return $resultado;
	}
//
}

?>
