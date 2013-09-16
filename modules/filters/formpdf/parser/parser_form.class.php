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



class form extends ParserRoot {

	var $x;
	var $y;
	var $width;
	var $height;
	var $action;
	var $method;

	function get_content_child($child, $element = null) {
		if ($element) {
			$etiqueta = & $element-> getElementsByPath($child);
		} else {
			$etiqueta = & $this->element-> getElementsByPath($child);
		}
		$etiqueta = $etiqueta->item(0);
		$etiqueta = $etiqueta->getText();
		$etiqueta = $this->special_chars($etiqueta);

		return $etiqueta;
	}

	function build($modo) {
		// Element style
		$array_style = $this->style->get_style_element($this->element);

		// preparing box
		$this->renderer->setDim($array_style['width'], $array_style['height']);
		$this->renderer->setPos($array_style['x'], $array_style['y']);
		$this->renderer->setStyle($array_style);
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class')));
		$this->renderer->setUrl($this->element->getAttribute('url'));
		$this->renderer->setMethod($this->element->getAttribute('method'));
		$this->renderer->setIndep($this->element->getAttribute('indep'));

		if ($modo == "apertura") {
			$buffer = $this->renderer->open_form ();
			fwrite ($this->handler, $buffer);
			$script = $this->js_associated ();
			if (!empty ($script)) {
				fwrite ($this->handler, $script);
			}
		}
		elseif ($modo == "cierre") {
			$buffer = $this->renderer->close_form ();
			fwrite ($this->handler, $buffer);
		}
	}
}

?>
