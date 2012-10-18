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



class Latex_hyperlink extends Latex {

	var $x;
	var $y;
	var $width;
	var $style_name;
	var $uri;
	var $title;

	function setPos($x,$y){
		$this->x = $x;
		$this->y = $y;
	}

	function setWidth($w){
		$this->width = $w;
	}

	function setStyle($class_name){
		$this->style_name = $class_name;
	}

	function setURI($uri){
		$this->uri = $uri;
	}

	function setTitle ($title) {
		$this->title = $title;
	}

	function add_hyperlink() {
		if (!empty ($this->class)) {
			$buffer  = "\\". $this->class. "\n";
		}

		$this->title = $this->filterSpecialChars($this->title);

/*
 * For some reason, expasion of macro fails.
 *
		$buffer .= "\\enlace{".$this->x."}{".$this->y."}".
					"{".$this->width."}{".$this->uri."}".
					"{".$this->title."}\n";
*/
		$buffer .= "\\begin{textblock}{".$this->width."}(".$this->x.", ".$this->y.")\n".
					"\\href{".$this->uri."}{".$this->title."}\n".
					"\\end{textblock}\n";

		return $buffer;
	}
//
}

?>
