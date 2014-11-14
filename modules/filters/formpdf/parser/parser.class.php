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
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../../"));

// DOMIT!
ModulesManager::file("/extensions/domit/xml_domit_include.php");
ModulesManager::file("/extensions/domit/xml_domit_shared.php");
ModulesManager::file("/extensions/domit/xml_domit_parser.php");
ModulesManager::file("/extensions/domit/xml_domit_utilities.php");
ModulesManager::file("/extensions/domit/xml_domit_getelementsbypath.php");
ModulesManager::file("/extensions/domit/xml_domit_nodemaps.php");
ModulesManager::file("/extensions/domit/xml_domit_cache.php");
ModulesManager::file("/extensions/domit/xml_saxy_parser.php");
ModulesManager::file("/extensions/domit/xml_domit_doctor.php");
ModulesManager::file("/extensions/domit/php_file_utilities.php");

// TODO: Include just necessary files

// common class
ModulesManager::file("/formpdf/parser/parser_document.class.php", 'filters');
ModulesManager::file("/formpdf/latex/latex_document.class.php", 'filters');
ModulesManager::file("/formpdf/parser/block_analyzer.class.php", 'filters');
ModulesManager::file("/formpdf/parser/parser_style.class.php", 'filters');
ModulesManager::file("/formpdf/parser/parser_script.class.php", 'filters');

// latex parser
ModulesManager::file( "/formpdf/Latex.class.php", 'filters');
ModulesManager::file("/formpdf/parser/parser_root.class.php", 'filters');
ModulesManager::file("/inc/fsutils/FsUtils.class.php");

class Parser {

	var $input_file;
	var $output_file;
	var $aux;
	var $unit;

	// nodes which it parsing is not recursive and it do it own parser.
	var $specialNodes;

	function Parser() {
		// nodes with special processing for parser
		$this->specialNodes = array(
				"paragraph",
				"attach",
				"select",
				"button",
				"listing"
		);
	}

	function set_input($filename) {
		$this->input_file = $filename;
	}

	function set_output($filename) {
		$this->output_file = $filename;
		$posicion = strrpos($filename, ".");
		if ($posicion === false) {
			$this->aux = $filename . ".tex";
		} else {
			$this->aux = substr($filename, 0, $posicion) . ".tex";
		}
	}

	function build() {
		$mydoc =& new DOMIT_Document();
		$exito = $mydoc->loadXML($this->input_file);

		if (!$exito) {
			//echo "Error while loading XML";
			XMD_Log::write("Error while loading XML", 8);
		}
		else {
			$doc_element = $mydoc->documentElement;
			if ($mydoc->documentElement->hasChildNodes()) {
				/**
				 * Generates the styles.tex translating to 'latex macros' all the elements of the style sheet				 * 					and then can call its on each element creation which contains this style.
				 */

				// Stylee construction
				$nodes = $doc_element->getElementsByTagName("link");
				$nodes = $nodes->arNodeList;
				$style = new Style($nodes);
				$style->build();

				$mydoc = & $mydoc->documentElement;

				$handler = fopen($this->aux, "w");
				// Document construction
				$document = new document();

				$document->renderer =& new latex_document();
				$document->open_document($handler, $doc_element);
				$this->unit = $document->getUnit();
				// put_javascript is in latex.class.php and latex_javascript.class.php
	//			$document->put_javascript($mydoc, $handler);
	//			$document->open_form($handler);

				// Obtaining of canvas...
				$canvas = $doc_element->getElementsByTagName("canvas");
				// This make canvas be a children nodes list of the first node (the <canvas> node)
				$canvas = $canvas->arNodeList[0];

				$lang = Languages::getISO639_1Name($canvas->getAttribute("lang"));
				$buffer = "\\selectlanguage{".$lang."}\n";
				fwrite($handler, $buffer);

				// Style construction
				$nodes = $doc_element->getElementsByTagName("script");
				$nodes = $nodes->arNodeList;
				$scriptJs = new ScriptJS($nodes);
				$scriptJs->handler = $handler;
				$scriptJs->renderer =& new Latex_scriptjs();
				$scriptJs->build();

				$this->px2mmX = $canvas->getAttribute("px2mmx");
				$this->px2mmY = $canvas->getAttribute("px2mmx");

				/**
				 * It creates an instance of and do and scanNodes to
				 * traverse all the XML tree. Into scanNodes it pays attention to 
				 * special nodes. If one is found, it goes to the next brother (nextSibling)
				 */
				$blockAnalizer =& new block_analyzer;
				$blockAnalizer->handler =& $handler;
				$blockAnalizer->style_obj =& $style;
				$this->scanNodes($canvas, $blockAnalizer);

				// It closes the form
	//			$document->close_form($handler);

				// It closes the document
				$document->close_document($handler);

				fclose($handler);

				// Execution of pdflatex
				$this->_process();
			}
		}
	}

	/**
	 * Method to traverse recursively the XML tree based on a given node
	 * It checks type of last node. It just analyze directly nodes with nodeType=1
	 * Nodes with nodeType = 3 (#text) should be ignored.
	 * Special nodes (Parser::specialNodes) should
	 * have a more detailed processing.
	 * TODO: Inlude support for comments:
	 * Nodes with nodeType = 8 (#comment) should be ignored
	 * NOTE: to controll a comment it need to know that $rootNode->firstChild is the comment
	 */
	function scanNodes($rootNode, &$blockAnalizer) {
		if ($rootNode->hasChildNodes() && ($rootNode->nodeType != DOMIT_TEXT_NODE)) {

			// Margins should be processed at the end because so latex will put it
			// on the other elements.
			$margins = array();

			for ($i = 0; $i < $rootNode->childCount; $i++) {

				$nodo = $rootNode->childNodes[$i];

				if (strtolower($nodo->nodeName) == 'margin') {
					$margins[] = $nodo;
				} else {
					$this->_processNode($nodo, $blockAnalizer);
				}
			}

			// Now it is processing nodes
			foreach ($margins as $margin) {
				$this->_processNode($margin, $blockAnalizer);
			}
		}
	}

	// Auxiliary function forscanNodes
	function _processNode($nodo, &$blockAnalizer) {

		// Here using analyzer already instantiated
		$blockAnalizer->root =& $nodo;
		// If node has children nodes
		// and it is not a special node
		// proccess is repeated recursively
		if (strtolower($nodo->nodeName) == "form") {
			// Instanciate and initialize the element renderer
			$element = $blockAnalizer->createElementRenderer();
			// Renders the element
			$element->setUnit($this->unit);
			$element->setPx2mmX($this->px2mmX);
			$element->setPx2mmY($this->px2mmY);
			$element->style->setUnit($this->unit);
			$element->build("apertura");
		} else {
			$blockAnalizer->analyze($this->unit);
		}
		if ($nodo->hasChildNodes() && !$this->isSpecialNode($nodo)) {
			$this->scanNodes($nodo, $blockAnalizer);
		}
		if (strtolower($nodo->nodeName) == "form") {
			// Renders the element
			$element->setUnit($this->unit);
			$element->style->setUnit($this->unit);
			$element->build("cierre");
		}
	}

	function isSpecialNode($node) {
		if (in_array($node->nodeName, $this->specialNodes)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Call to pdflatex
	 */
	function _process() {
		// File command.tex is copied to /tmp
		if (file_exists(XIMDEX_ROOT_PATH."/formpdf/latex/command.tex")) {
			$cmd = "cp ".XIMDEX_ROOT_PATH."/formpdf/latex/command.tex /tmp/command.tex";
			exec($cmd);
		}

		// transformation to pdf
		$cmd = "cd /tmp && pdflatex -interaction nonstopmode " . $this->aux;
		exec($cmd);

		$cmd = "cp " . $this->output_file . ".pdf " . $this->output_file;
		exec($cmd);
	}

// end parser
}

?>