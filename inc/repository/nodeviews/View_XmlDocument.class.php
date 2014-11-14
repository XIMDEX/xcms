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

if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_XmlDocument extends Abstract_View implements Interface_View {

/**
 * @param int $idVersion
 * @param string $pointer
 * @param mixed $args
 * @return string
 */
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {

		$this->retrieveContent($pointer);
		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error("El nodo que se est� intentando convertir no existe: $idNode");
			return false;
		}

		$name = $node->get('Name');
		$nodeType = $node->getNodeType();
		if ($nodeType != 5032 && $name != 'docxap.xsl') return false;

		if ($nodeType == 5032) {
			// Si $content == null se insertan las etiquetas docxap, en caso contrario se eliminan
			if (is_null($content)) {
				$content = $this->addDocxap($node);
			} else {
				$content = $this->delDocxap($content);
			}
		} else {
			// Si $content == null se insertan las css, en caso contrario se eliminan
			if (is_null($content)) {
				$content = $this->insertStylesheet($node);
			} else {
				$content = $this->removeStylesheet($content);
			}
		}

		return $this->storeTmpContent($content);
	}

	private function addDocxap($node) {
		$content = $node->class->getRenderizedContent();

		$xslPath = $this->getXslPath($node);

		if ($xslPath !== null) {
			$xslPath = sprintf('<?xml-stylesheet type="text/xsl" href="{base_uri}%s"?>', $xslPath);
			$c = 1;
			$content = str_replace('?>', "?>\n".$xslPath, $content, $c);
		}

		return $content;
	}

	private function delDocxap($content) {

		$doc = new DOMDocument();
		$result = $doc->loadXML($content);
		if(!$result) return false;

		$docxap = $doc->getElementsByTagName('docxap');
		$docxap = $docxap->item(0);

		$childrens = $docxap->childNodes;
		$l = $childrens->length;

		$xmlContent = '';
		for ($i=0; $i<$l; $i++) {
			$child = $childrens->item($i);
			if ($child->nodeType == 1) {
				$xmlContent .= $doc->saveXML($child) . "\n";
			}
		}

		return $xmlContent;
	}

	private function getXslPath($node) {

		$docxap = null;
		$xslPath = null;

		while (null !== ($idparent = $node->getParent()) && $docxap === null) {

			unset($node);
			$node = new Node($idparent);
			$xslFolder = $node->GetChildByName('templates');

			if ($xslFolder !== false) {

				$xslFolder = new Node($xslFolder);
				$docxap = $xslFolder->class->getNodePath() . '/docxap.xsl';
				unset($xslFolder);
				if (!is_readable($docxap)) $docxap = null;
			}
		}

		if ($docxap !== null) {
			$xslPath = str_replace(XIMDEX_ROOT_PATH . '/data/nodes', '', $docxap);
		}

		return $xslPath;
	}

	private function insertStylesheet($node) {
		$css = $this->getStylesheets($node);
		$css = "<style id=\"docxap_stylesheet\" type=\"text/css\">\n" . $css . "\n</style>\n</head>\n";
		$content = $node->getContent();
		$content = str_replace('</head>', $css, $content);
		return $content;
	}

	private function removeStylesheet($content) {
		$regexp = '#(<style id="docxap_stylesheet"(?:.*)</style>)#is';
		$content = preg_replace($regexp, '', $content);
		return $content;
	}

	private function getStylesheets($node) {

		$last = null;
		$content = '';

		while (null !== ($idsection = $node->getSection())) {


			if ($idsection != $last) {
				$last = $idsection;
				unset($node);
				$node = new Node($idsection);
				$cssFolder = $node->GetChildByName('css');

				if ($cssFolder !== false) {
					$cssFolder = new Node($cssFolder);
					// 5028 => CssFile
					$cssList = $cssFolder->GetChildren(5028);
					foreach ($cssList as $css) {
						$css = new Node($css);
						$content .= $css->getContent() . "\n";
						unset($css);
					}
					unset($cssFolder);
				}
			}

			$idparent = $node->getParent();
			unset($node);
			$node = new Node($idparent);
		}

		return $content;
	}

}

?>