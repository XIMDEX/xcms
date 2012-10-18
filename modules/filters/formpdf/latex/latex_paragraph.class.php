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



class Latex_paragraph extends Latex {

	var $x;
	var $y;
	var $rot;
	var $width;
	var $height;
	var $content;
	var $style_name;
	var $fontSize;
	var $lineHeight;

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y - 2;
	}

	function setWidth($w){
		$this->width = $w;
	}

	function setHeight($w){
		$this->height = $w;
	}

	function setRotation($rot){
		$this->rot = $rot;
	}

	function setStyle($class_name){
		$this->style_name = $class_name;
	}

	function setAlign ($align) {
		$this->align = $align;
	}

	function setFontSize ($fontSize) {
		$this->fontSize = $fontSize;
	}

	function setLineHeight ($lineHeight) {
		$this->lineHeight = $lineHeight;
	}

 	function setContent($parrafo){
		$this->content = $parrafo;
 	}

	function add_paragraph() {
		if (!empty ($this->class)) {
			$buffer  = "\\". $this->class. "\n";
		}

		if (!empty ($this->align)) {
			if ($this->align == "left") {
				$align = "flushleft";
			}
			elseif ($this->align == "right") {
				$align = "flushright";
			}
			elseif ($this->align == "center") {
				$align = "center";
			}
		}
		else {
			$align = "flushleft";
		}

		$fontSize = (!empty ($this->fontSize)) ? $this->fontSize : "10";
		$lineHeight = (!empty ($this->lineHeight)) ? $this->lineHeight : "5";

		$this->content = $this->filterSpecialChars($this->content);
		$rotParam = "";
		if ($this->rot != 0) {
			$rotParam = "{".$this->rot."}";
			$this->y -= ($this->width / 2) * abs(sin(deg2rad($this->rot)));
			$this->x += ($this->width / 2) * (1 - abs(cos(deg2rad($this->rot))));
			if ($this->rot != 180 && $this->rot != 360) {
				$this->y += $this->width / 8;
				$this->x -= $this->width / 6;
			}
			$macroName = "\\parraforrotado";
		}
		else {
			$macroName = "\\parrafo";
		}
		$buffer .= $macroName."{".$this->x."}{".$this->y."}".$rotParam.
					"{".$this->width."}{".$this->content."}".
					"{".$align."}{".$fontSize."}{".$lineHeight."}\n";
		return $buffer;
	}
//
}

?>
