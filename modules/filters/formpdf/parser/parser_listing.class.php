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



class Listing extends ParserRoot {
	function _parseList($element) {
		$items = $element->childNodes;
		$num_items = $element->childCount;
		for($i=0; $i < $num_items ; $i++) {
			if ($items[$i]->nodeType == 3) {// Node type 3 = #text, it is obtained with getText;
				$array_items[] = utf8_decode ($items[$i]->getText());
			}
			if ($items[$i]->childCount > 0) {
				$subList = $this->_parseList($items[$i]);
				if (!empty($subList)) {
					$array_items[] = $this->_parseList($items[$i]);
				}
			}
		}
		return $array_items;
	}

	function build() {
		$array_style = $this->style->get_style_element($this->element);
		$this->renderer->setPos($array_style['x'], $array_style['y']);

		$this->renderer->setDim($array_style['width'], $array_style['height']);
		$type = $this->element->getAttribute("type");
		$this->renderer->setType($type);
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class')));

		$this->renderer->setStyle($array_style);

		$array_items = $this->_parseList($this->element);
		$this->renderer->setItems($array_items);

		$buffer = $this->renderer->add_list();
		fwrite($this->handler,$buffer);
	}

}

?>