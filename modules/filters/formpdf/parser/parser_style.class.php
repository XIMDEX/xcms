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




ModulesManager::file("/extensions/csstidy/class.csstidy.php");
ModulesManager::file("/formpdf/latex/latex_style.class.php", 'filters');
ModulesManager::file("/formpdf/parser/parser_root.class.php", 'filters');
ModulesManager::file("/inc/fsutils/FsUtils.class.php");
ModulesManager::file("/inc/xml/XmlParser.class.php");
ModulesManager::file("/inc/persistence/datafactory.php");

class Style extends ParserRoot {
	var $nodes;

	function Style($nodes) {
		$this->nodes = $nodes;
	}

	function _translateCanvasStyles($style) {
		$canvasStyles = array();
		$canvasStyles["background-color"] = "pagecolor";
		$ret = isset($canvasStyles[$style]) ? $canvasStyles[$style] : null;
		return $ret;
	}

	function build() {
		// Parses the style sheets of a XML document
		$this->_parse();

		// Resets the styles.tex file
		$fh = fopen("/tmp/styles.tex", "w");
		fclose($fh);

		$latexStyle = new Latex_style();
		$stylesToCreate = array("ButtonStyle" => "everyPushButton",
								"InputStyle" => "everyTextField",
								"ListStyle" => "everyComboBox",
								"CheckBoxStyle" => "everyCheckBox");
		reset($this->styles);
		while (list($name, $style) = each($this->styles)) {
			$latexStyle->make_def_style($this->convertClassNameToLatex($name, "Style"), $style);
			reset($stylesToCreate);
			while(list($styleSuffix, $command) = each($stylesToCreate)) {
				$latexStyle->make_acro_style($this->convertClassNameToLatex($name, $styleSuffix), $style, $command);
			}
		}
	}

	function _parse() {
		$cssParser = new csstidy();
		$cssStyles = array();

		// Parses style sheets
		foreach ($this->nodes as $node) {
			$nodeId = $node->getAttribute("nodeid");
			if ($nodeId != "default") {
				$dF = new DataFactory($nodeId);
				$fileName = Config::getValue("AppRoot").
							Config::getValue("FileRoot").
							"/".$dF->GetTmpFile($dF->GetLastVersionId());
			} else {
				$fileName = $node->getAttribute("filename");
			}
			if (!is_dir($fileName)) {
				$cssParser->parse(FsUtils::file_get_contents($fileName));
				reset($cssParser->css);
				while (list(, $cssArray) = each($cssParser->css)) {
					reset($cssArray);
					while (list($key, $value) = each($cssArray)) {
						if (strstr($key, ".")) {
							$procesedKey = substr($key, 1);
						} else {
							$procesedKey = $key;
						}
						$cssStyles[$procesedKey] = $value;
					}
				}
			}
		}

		// It selects just properties that interest us
		$validTags = array ("font-family",
							"font-size",
							"font-weight",
							"color",
							"background-color",
//							"border",
//							"border-style",
							"border-color");
		reset($cssStyles);
		while (list($cssClass, $cssValues) = each($cssStyles)) {
			reset($cssValues);
			while (list($propertyName, $propertyValue) = each($cssValues)) {
				if (!in_array($propertyName, $validTags)) {
					unset($cssStyles[$cssClass][$propertyName]);
				}
				else {
					if (!(strpos(strtolower($cssClass), "canvas") === false)) {
						unset($cssStyles[$cssClass][$propertyName]);
						$propertyName = $this->_translateCanvasStyles($propertyName);
						$cssStyles[$cssClass][$propertyName] = $propertyValue;
					}
				}
			}
		}
		$this->styles = $cssStyles;
	}
}
?>