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




/**
 * XIMDEX_ROOT_PATH
 */
if (!defined("XIMDEX_ROOT_PATH"))
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../../../"));


//
ModulesManager::file('/inc/utils.php');


class Block_analyzer {
	var $root ;
	var $handler;
	var $renderer;
	var $style_obj;

	// nodes which parser should ignore and which are destined 
	//to things of graphic edition
	
	var $ignoreNodes;

	function block_analyzer() {
		// Nodes which parser must ignore
		/**
		 * TODO: With support for comments complete, it should delete #comment
		 * as part of ignore nodes.
		 */
		$this->ignoreNodes = array(
				"group",
				"#comment"
		);
	}

	/**
	 * analyze (this). Method to analyze a given node.
	 * Analysis is to detect which renderer it is necessary to use.
	 */
	function analyze ($unit = "") {
		// root is a reference to actual node.
		// Name of node is enought to rendering.
		// It just render nodes which should not be ignored

		// TODO: Use Factory.class.php in $XIMDEX_ROOT_PATH/inc/patterns
		$element_class = $this->root->nodeName;
		if (!$this->ignoreNode($this->root->nodeName)) {
			// It instanciates and sets up the element renderer
			$element = $this->createElementRenderer ();
			// Renders the element
			if ($element) {
				$element->style->setUnit($unit);
				$element->setUnit($unit);
				$element->build();
			}
		}
	}

	// It instanciates and sets up the element renderer
	function createElementRenderer () {
		$element = false;
		$element_class = $this->root->nodeName;
		$parser = XIMDEX_ROOT_PATH.ModulesManager::path('filters')."/formpdf/parser/parser_".$element_class.".class.php";
		$renderer = XIMDEX_ROOT_PATH.ModulesManager::path('filters')."/formpdf/latex/latex_".$element_class.".class.php";
		if (file_exists ($parser) && file_exists ($renderer)) {
		
			require_once($parser);
			require_once($renderer);
			if (class_exists($element_class)) {
				$element =& new $element_class($this->root);
				$element->handler =& $this->handler;

				// Instanciates renderer of this element
				$latex_renderer = "Latex_".$element_class;
				if (class_exists($latex_renderer)) {
					$element->renderer =& new $latex_renderer;
				}

				// and style
				$element->style =& $this->style_obj;
			}
		}
		else {
			XMD_Log::write("It could not be possible to load the renderer element (".$renderer.")",  9);
		}
		return $element;
	}

	function ignoreNode($nodename) {
		if (in_array($nodename, $this->ignoreNodes)) {
			return true;
		} else {
			return false;
		}
	}
}
?>