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




class Box extends ParserRoot{


	var $x;
	var $y;
	var $width;
	var $height;

	function build() {
		// element styles
		$array_style = $this->style->get_style_element($this->element);

		// preparing box
		$this->renderer->setDim($array_style['width'], $array_style['height']);
		$this->renderer->setPos($array_style['x'], $array_style['y']);
		$this->renderer->setStyle($array_style);
		$this->renderer->setBorderWidth($array_style['border_width']);
		$this->renderer->setBorderRound($array_style['border_round']);
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class')));

		$buffer = $this->renderer->add_box(/*$data, $array_style*/);
		fwrite($this->handler, $buffer);
	}

//
}

?>