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



class ParserRoot {

	var $handler;
	var $renderer;
	var $style;
	var $element;
	var $env;
	var $unit;
	var $px2mmX;
	var $px2mmY;

	// Method of builder to assign the objects passed by reference
	function ParserRoot(& $element) {
		$this->element = $element;
	}

	function setUnit($unit) {
		$this->unit = $unit;
	}
	function setPx2mmX($px2mmX) {
		$this->px2mmX = $px2mmX;
	}
	function setPx2mmY($px2mmY) {
		$this->px2mmy = $px2mmY;
	}

	function getUnit() {
		return $this->unit;
	}

	function special_chars($string) {
		//$string = utf8_decode ($string);
		//$busqueda = array("@á@", "@é@", "@í@", "@ó@", "@ú@", "@ñ@", "@Á@", "@É@", "@Í@", "@Ó@", "@Ú@", "@Ñ@");
		//$reemplazar = array("'a", "'e", "'i", "'o", "'u", "~n", "'A", "'E", "'I", "'O", "'U", "~N");
		//$string = preg_replace($busqueda, $reemplazar, $string);
		return $string;
	}

	function gif2jpg($imagen, $filename) {
		$im = imagecreatefromgif($imagen);
		imagejpeg($im, $filename);
	}
	/**
	 * Function held because of compatibility, All its references in the code should be deleted 
	 *
	 */
/*
	function convertClassNameToLatex($className, $suffix = 'Style') {
		$className = str_replace('_', '', $className);
		return sprintf('%s%s', $className, $suffix);
	}
*/
	function convertClassNameToLatex($className, $suffix = '') {
		if (!empty ($className)) {
			$suffix = empty ($suffix) ? "Style" : $suffix;
			$className = str_replace('_', '', $className);
			return sprintf('%s%s', $className, $suffix);
		}
	}
	function get_style_element(& $element) {
		$node_name = $element->nodeName;
		$array_style = array();

		if ($element->hasAttribute("class")) {
			$array_style["class"] = $element->getAttribute("class");
			//$class_name = $element->getAttribute("class");
		}

		if ($element->hasAttribute("width")) {
			$array_style["width"] = $this->convert_to_mm($element->getAttribute("width"), "x");
		}

		if ($element->hasAttribute("height")) {
			$array_style["height"] = $this->convert_to_mm($element->getAttribute("height"), "y");
		}

		if ($element->hasAttribute("x")) {
			$array_style["x"] = $this->convert_to_mm($element->getAttribute("x"), "x");
		}

		if ($element->hasAttribute("y")) {
			$array_style["y"] = $this->convert_to_mm($element->getAttribute("y"), "y");
		}

		if ($element->hasAttribute("border_width")) {
			$borderWidth = $element->getAttribute("border_width");
			if ($borderWidth > 2) $borderWidth -= 2;
			$array_style["border_width"] = $this->convert_to_mm($borderWidth, "x");
		}

		if ($element->hasAttribute("border_round")) {
			$array_style["border_round"] = $this->convert_to_mm($element->getAttribute("border_round"), "x");
		}

		if ($element->hasAttribute("font-size")) {
			$array_style["font-size"] = $this->convert_to_mm($element->getAttribute("font-size"), "x") / 0.3514598035;
		}

		if ($element->hasAttribute("line-height")) {
			$array_style["line-height"] = $this->convert_to_mm($element->getAttribute("line-height"), "x") / 0.3514598035;
		}

		return $array_style;
	}

/*
 * Obsolete function: the spacing is defined with same instruction that text size
 *
	function translateLineHeight($lineHeight) {
		switch ($lineHeight) {
			case "100%":
				$lineHeight = "singlespacing";
			break;

			case "150%":
				$lineHeight = "onehalfspacing";
			break;

			case "200%":
				$lineHeight = "doublespacing";
			break;

			default:
				$lineHeight = "";
			break;
		}
		return $lineHeight;
	}
*/

	function convert_to_mm($size, $xy = "") {
		preg_match("`([0-9,\.]+)(px|cm|pt|mm)?`", $size, $matches);
		if (isset($matches[2])) {
			$size = $matches[1];
			$unit = $matches[2];
		} else {
			$unit = $this->unit;
		}
		if (!empty($size) && $unit != "mm") {
			switch ($unit) {
				case "cm" :
					$size *= 10;
					break;

				case "px":
					//$size *= ($xy == "x") ? $this->px2mmX : $this->px2mmY;
					$size = round($size / 3, 10);
					break;

				case "pt":
					$size *= 25.4/72;
					break;

				default:
					$size = $size;
					break;
			}
		}
		return $size;
	}

	function js_associated () {
		$script = $this->element->getAttribute ("script");
		if (!empty ($script)) {
//			$res = "\\JS{".trim ($this->element->getAttribute ("script"))."}";
			$res = trim ($this->element->getAttribute ("script"));
		}
		else {
			$res = "";
		}

		return $res;
	}
}
?>