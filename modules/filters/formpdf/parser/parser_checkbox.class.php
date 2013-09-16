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



if (!defined ("XIMDEX_ROOT_PATH")) {
		define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../../');
	}
	
include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
ModulesManager::component('/formpdf/parser/parser_root.class.php', 'filters');


Class checkbox extends ParserRoot {
	var $x;
	var $y;
	var $width;
	var $height;
	var $enabled;

	function build() {

		$data = array();
		// element styles
		$array_style = $this->style->get_style_element($this->element);

		// prepararing box
		$this->renderer->setDim($array_style['width'], $array_style['height']);
		$this->renderer->setPos($array_style['x'], $array_style['y']);
		$this->renderer->setEnabled($this->element->getAttribute('enabled'));
		$this->renderer->setName($this->element->getAttribute('name'));
		$this->renderer->setClass($this->convertClassNameToLatex($this->element->getAttribute('class'), 'CheckBoxStyle'));
		$this->renderer->setScript($this->js_associated ());

		$buffer = $this->renderer->add_checkbox($data, $array_style);
		fwrite($this->handler, $buffer);
	}

//
}

?>
