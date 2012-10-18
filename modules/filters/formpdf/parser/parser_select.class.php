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



class Select extends ParserRoot {

	function build() {

		$showAs = $this->element->getAttribute('showas');

		$array_style = $this->style->get_style_element($this->element);
		// Element rendering
		$this->renderer->setPos($array_style['x'], $array_style['y']);

		$this->renderer->setDim($array_style['width'], $array_style['height']);

		$type = $this->element->getAttribute('type');
		$this->renderer->setType($type);
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class'), 'ListStyle'));
		$this->renderer->setScript($this->js_associated ());
		$this->renderer->setStyle($array_style);

		$name = $this->element->getAttribute("name");
		$this->renderer->setName($name);

		$opciones = & $this->element->getElementsByTagName("item");
		$lista_opciones = array();

		for ($o = 0; $o < $opciones->getLength(); $o++) {
			$opcion = $opciones->item($o);

			// It detected which element is by default 
			if ($opcion->hasAttribute('default') && $opcion->getAttribute('default') == 'yes') {
				$indice_default = $o;
			}

			$label = utf8_decode ($opcion->firstChild->gettext());
			$value = $opcion->childNodes[1]->gettext ();
			$lista_opciones[$value] = $label;
		}

		// It sets the default renderer
		$this->renderer->setDefault($indice_default);

		//It sets options
		$this->renderer->setOptions($lista_opciones);

		// It build the element according to $showAS
		$buffer = $this->build_select($showAs);
		fwrite($this->handler, $buffer);
	}

	function build_select($mostrar_como) {

		switch ($mostrar_como) {
			case 'combo':
				return $this->renderer->create_combo();
				break;

			case 'checkbox':
			case 'check':
				return $this->renderer->create_checkbox();
				break;

			case 'radio':
				return $this->renderer->create_radio();
				break;

			default:
				break;
		}
	}
}

?>
