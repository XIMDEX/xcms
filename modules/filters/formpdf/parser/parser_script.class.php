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



ModulesManager::file('/formpdf/parser/parser_root.class.php', 'filters');
ModulesManager::file('/formpdf/latex/latex_script.class.php', 'filters');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/xml/XmlParser.class.php');

class ScriptJS extends ParserRoot {
	var $nodes;

	function ScriptJS($nodes) {
		$this->nodes = $nodes;
	}

	function build() {
		// Parses style sheets
		foreach ($this->nodes as $node) {
			$file = $node->getAttribute("filename");
			if (is_file($file)) {
				$routeToScript = $file;
				$content = FsUtils::file_get_contents($routeToScript);
				// Checks content
				if (!(strpos(strtolower($content), "javascript") === false)) {
					$content = trim($content);
					$funcion = preg_match_all ("|function[ ]+(.*)[\(](.*)[\)][ ]*\{|U", $content, $out, PREG_OFFSET_CAPTURE);
					$eliminar = $out[0][0][0];
					$content = substr($content,strlen($eliminar)+1);
					$content = rtrim($content,"}");
					$content = trim($content);
					$script_name = $out[1][0][0];

					$buffer = $this->renderer->add_script($script_name, $content);
					fwrite($this->handler,$buffer);
				}
			}
		}
	}
}
?>
