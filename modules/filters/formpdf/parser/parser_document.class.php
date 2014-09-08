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




if (!defined("XIMDEX_ROOT_PATH"))
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../../"));

ModulesManager::file('/formpdf/parser/parser_root.class.php', 'filters');

class Document extends ParserRoot {

	var $sendTo;
	var $renderer;

	function document() {
	}

	function open_document(&$handler, &$doc_element) {
		// Capture of nod
		$canvas = $doc_element->getElementsByTagName("canvas");
		$canvas = $canvas->arNodeList[0];
		// Capture of language
		$lang = Languages::getISO639_1Name($canvas->getAttribute("lang"));

		// Packages
		$packages = array();
		$packages["babel"] = array($lang, "activeacute");
		$packages["hyperref"] = "colorlinks";
		$packages["eforms"] = "pdftex";
		$packages["graphicx"] = "pdftex";
		$packages["color"] = "pdftex";
		$packages["fancybox"] = "";
		$packages["attachfile"] = "";
		$packages["helvet"] = "scaled";
		$packages["array"] = "";
		$packages["colortbl"] = "";
		$packages["multirow"] = "";
		$packages["tikz"] = "";
		$packages["underscore"] = "strings";
		$packages["textpos"] = "";
		$packages["textcomp"] = "";
		$packages["setspace"] = "";
		//$packages["caption"] = "";

		// Canvas configuration
		$this->unit = $canvas->getAttribute("unit");
		$this->px2mmX = $canvas->getAttribute("px2mmx");
		$this->px2mmY = $canvas->getAttribute("px2mmy");
		$style["pagewidth"] = $this->convert_to_mm($canvas->getAttribute("width"), "x")."mm";
		$style["pageheight"] = $this->convert_to_mm($canvas->getAttribute("height"), "y")."mm";

//		$style["topmargin"] = ($this->convert_to_mm($canvas->getAttribute("topmargin"), "y") - 29)."mm";
//		$style["oddsidemargin"] = ($this->convert_to_mm($canvas->getAttribute("leftmargin"), "x") - 25.4)."mm";
//		$style["evensidemargin"] = ($this->convert_to_mm($canvas->getAttribute("leftmargin"), "x") - 25.4)."mm";

		// NOTE: it set margins always to zero.
		// Process to show margins is performed in the javascript.
		$style["topmargin"] = "-29mm";
		$style["oddsidemargin"] = "-25.4mm";
		$style["evensidemargin"] = "-25.4mm";

		$style["headheight"] = $this->convert_to_mm($canvas->getAttribute("headheight"), "y")."mm";
		$style["headsep"] = $this->convert_to_mm($canvas->getAttribute("headsep"), "y")."mm";
		$style["textheight"] = $style["pageheight"];
		$style["textwidth"] = $style["pagewidth"];
		$style["text"] = "10pt";
		$style["language"] = "spanish";
		$style["docType"] = "article";
		$style["orientation"] = $canvas->getAttribute("orientation");
		$style["dim"] = $canvas->getAttribute("dim") . "paper";

		$buffer = $this->renderer->open_document($style, $packages);

		fwrite($handler, $buffer);
	}

	function put_javascript(& $formulario, & $handler) {
		$jsElements = & $formulario->getElementsByPath("//script");

		if ($jsElements->getLength() > 0) {
			$javascript = array();
			for ($j = 0; $j <$jsElements->getLength(); $j++) {
				$nodeScript = $jsElements->item($j);
				$name = $nodeScript->getAttribute('name');
				// Checking if it is passed an nodeID or a js file
				$path_js = $nodeScript->getAttribute('url');

				if ((int) preg_match('/.js/', $path_js) == 0) {
					// In this case there is a nodeID pointing to the js file
					$node = new Node($path_js);
					if ($node->GetID() > 0) {
						$path_js = $node->class->GetNodePath();
					}
				}

				// It opens file to obtain its content
				if (is_file($path_js)) {
					$fp = fopen($path_js, 'r');
					$codigo = fread($fp, filesize($path_js));
					fclose ($fp);
					$javascript[$name] = $codigo;
					$buffer = $this->renderer->put_javascript($javascript);
				}
			}
		}
		fwrite($handler, $buffer);
	}

	function open_form(& $handler) {
		$buffer =  $this->renderer->form->open_form();
		fwrite($handler, $buffer);
	}

	function close_form(& $handler) {
		$buffer =  $this->renderer->form->close_form();
		fwrite($handler,$buffer);
	}

	function close_document(& $handler) {
		$buffer =  $this->renderer->close_document();
		fwrite($handler,$buffer);
	}
}

?>