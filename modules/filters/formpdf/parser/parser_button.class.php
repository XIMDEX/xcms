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




class Button extends ParserRoot {

	var $style;

	function build() {
		// element style
		$array_style = $this->style->get_style_element($this->element);
		$this->renderer->setStyle($array_style);
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class'), 'ButtonStyle'));
		$this->renderer->setLabel(utf8_decode ($this->element->getAttribute('label')));
		$this->renderer->setScript($this->js_associated ());
		$this->renderer->setPos($array_style['x'], $array_style['y']);
		$this->renderer->setDim($array_style['width'], $array_style['height']);
		$this->renderer->setName($this->element->getAttribute('name'));
		$this->renderer->setButtonType($this->element->getAttribute('type'));
		$buffer = $this->renderer->create_button();
		fwrite($this->handler, $buffer);
	}
}

?>