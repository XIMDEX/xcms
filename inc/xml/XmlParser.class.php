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




include_once(XIMDEX_ROOT_PATH."/inc/xml/XML.class.php");

class XmlParser extends XML {

	protected $xmlArray = null;
	protected $numItems = 0;
	protected $lastTag = null;

//	function __construct() {
//		parent::__construct();
//	}

	// Destruye la instancia del parser xml
	public function __destruct() {
	}

	// Devuelve el número de elementos
	public function length() {
		return $this->numItems;
	}

	public function load () {
		$this->numItems = 0;
		$this->xmlArray = null;
		$this->lastTag = null;
		parent::load();
	}

	// ----- DEBUG -----
//	public function getNode($index=null) {
//		if ($index === null) {
//			return $this->xmlArray;
//		} else {
//			return $this->xmlArray[$index];
//		}
//	}
	// ----- DEBUG -----

	public function getXmlArray() {
		$arr = is_array($this->xmlArray) ? $this->xmlArray : array();
		return $arr;
	}

	// Devuelve el valor de un nodo
//	public function getNodeValue($tagName, $index = 0) {
//		for ($i = 0; $i < count($this->xmlArray); $i++) {
//			if ($index ==  $i) {
//				$value = null;
//				if (isset($this->xmlArray[$i][$tagName]) && isset($this->xmlArray[$i][$tagName]["data"])) {
//					$value = $this->xmlArray[$i][$tagName]["data"];
//				} else {
////					var_dump($this->xmlArray[$i]);
//				}
//				return $value;
//			}
//		}
//	}
//
//	// Devuelve el valor de un atributo
//	public function getAttribute($tagName, $attrName, $index = 0) {
//		for ($i = 0; $i < count($this->xmlArray); $i++) {
//			if ($index == $i) {
//				$value = null;
//				if (isset($this->xmlArray[$i][$tagName]) && isset($this->xmlArray[$i][$tagName]['attr'][$attrName])) {
//					$value = $this->xmlArray[$i][$tagName]['attr'][$attrName];
//				}
//				return $value;
//			}
//		}
//	}


	// Procesa una etiqueta de apertura
    protected function _tag_open($parser, $tag, $attribs) {

    	parent::_tag_open($parser, $tag, $attribs);
		$tag = strtolower($tag);

		$tagArray = array();
		$tagArray['__parent__'] = null;
		$tagArray['tagName'] = $tag;
		$tagArray['data'] = null;
		$tagArray['attr'] = array();
		$tagArray['childrens'] = array();

		foreach ($attribs as $key => $value) {
			$key = strtolower($key);
			//$value = $this->recodeSrc($value, $this->encoding);
			$tagArray['attr'][$key] = trim($value);
		}

		if ($this->xmlArray === null) {
			$this->xmlArray = array();
			$this->xmlArray[$tag] =& $tagArray;
			$this->lastTag =& $this->xmlArray[$tag];
			$this->lastTag['__parent__'] = null;
		} else {
			$this->lastTag['childrens'][][$tag] =& $tagArray;
			$parent =& $this->lastTag;
			$this->lastTag =& $tagArray;
			$this->lastTag['__parent__'] =& $parent;
		}
    }

	// Procesa el contenido de una etiqueta
    protected function _tag_data($parser, $cdata) {
    	parent::_tag_data($parser, $cdata);
		$cdata = trim($cdata);
		//$cdata = $this->recodeSrc($cdata, $this->encoding);
		$this->lastTag['data'] .= $cdata;
    }

	// Procesa las etiquetas de cierre
    protected function _tag_close($parser, $tag) {
    	parent::_tag_close($parser, $tag);
    	$parent =& $this->lastTag['__parent__'];
    	unset($this->lastTag['__parent__']);
    	if ($parent !== null) $this->lastTag =& $parent;
		$this->numItems++;
    }
}
?>
