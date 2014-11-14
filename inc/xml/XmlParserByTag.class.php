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

//Parseador por tags o id, en vez de por arbol.
class XmlParserByTag extends XML {

	// ---------------------  
	public $elements = array();
	protected $byId = array();
	protected $lastTag = null;
	protected $numItems;
	protected $nivel;
	protected $parents = array();
	const BY_TAG = 1;
	const BY_ID = 2;
// //	function __construct() {
// //		parent::__construct();
// //	}

	// Destruye la instancia del parser xml
	public function __destruct() {
	}

	// Devuelve el número de elementos
	public function length() {
		return $this->numItems;
	}

	// ----- DEBUG -----
	public function getXmlArray($_type = self::BY_TAG, $_tag = NULL ) {
		if($_type == self::BY_TAG  & $_tag != NULL ) {
			return $this->elements["{$_tag}"];
		}else if($_type == self::BY_TAG) {
			return $this->elements;
		}else if($_type == self::BY_ID) {
			return $this->byId;
		}else {
			return null;
		}
	}

	public function load () {
		$this->elements = array();
		$this->byId = array();
		$this->lastTag = null;
		$this->numItems = 0;
		$this->nivel = 0;
		$this->parents[0] = 0;
		parent::load();

		/* Esto es un barrido de los arrays por tags para añadir en children sus respetivos hijos ( sólo se añaden los hijos de UN nivel por debajo ).*/
		//Barrido de elementos(padres)
		for($i = 1; $i <= $this->numItems; $i++) {
			$nivel = $this->byId[$i]["nivel"];
			//Segundo barrido buscando sus hijos.
			for($j = $i+1; $j<=$this->numItems && $nivel < $this->byId[$j]["nivel"]; $j++) {
				if($this->byId[$j]["parent"] == $i  ) {
					//Necesarios
					$tag = $this->byId[$i]["tagName"];
					$tag_children = $this->byId[$j]["tagName"];
					$index = $this->byId[$i]["index_children"];
					//Es un hijo. Toca asociarlo ( primero por tag )
					$z = count($this->elements[$tag][$index]["children"]);
					$this->elements[$tag][$index]["children_by_id"][$z] = $this->byId[$j];
					//Es un hijo. Toca asociarlo ( primero por elementos )
					$this->byId[$i]["children_by_id"][$z] = $this->byId[$j];
					//**** ahora por tag ***
					if (isset($this->elements[$tag][$index]["children"][$tag_children])) {
						$x = count($this->elements[$tag][$index]["children"][$tag_children]);
					} else {
						$x = 0;
					}
					$this->elements[$tag][$index]["children"][$tag_children][$x] =  $this->byId[$j];
					$this->byId[$i]["children"][$tag_children][$x]  = $this->byId[$j];

				}

			}
		}

	}


	// Procesa una etiqueta de apertura
	//Se encarga básicamente de crear un array por cada tag, también por id.
	//Así tendremos arrays como: link, image,,... que contiene a todos los enlaces e imágenes respectivamente.
    protected function _tag_open($parser, $tag, $attribs) {

    	parent::_tag_open($parser, $tag, $attribs);
		$tag = strtolower($tag);
		$i = $this->nivel++;
		$num_item = ++$this->numItems;
		if(isset($this->elements[$tag]))
			$j = count($this->elements[$tag]);
		else
			$j = 0;
		$parent = $this->parents[$i];

		$tagArray = array();

		$tagArray["parent"] = $parent;
		$tagArray["parent_tag"] = $this->byId[$parent]["tagName"];
		$tagArray["id"] = $num_item;
		$tagArray["index_children"] = $j;
		$tagArray["tagName"] = $tag;
		$tagArray["nivel"] = $i;
		$tagArray["data"] = null;
		$tagArray["attr"] = $attribs;
		$tagArray["children"] = null;
		$tagArray["children_by_id"] = null;

		$this->lastTag = $tag;

		$this->elements[$tag][$j] = $tagArray;
		$this->byId[$num_item] = $tagArray;
		$this->parents[$i+1] = $num_item;
		
    }

	// Procesa el contenido de una etiqueta
    protected function _tag_data($parser, $cdata) {
    	parent::_tag_data($parser, $cdata);
		
		$tag = $this->lastTag;
		$num_item = $this->numItems;

		$j = count($this->elements[$tag]);
		$j--; //Contamos uno menos, porque se está añadiendo el mismo
		if(!isset($this->elements[$tag][$j]["data"])) {
					$this->elements[$tag][$j]["data"] = $cdata;
					$this->byId[$num_item]["data"] = $cdata;
		}
		else
		{
			$this->elements[$tag][$j]["data"] = $this->elements[$tag][$j]["data"].$cdata;
			$this->byId[$num_item]["data"] = $this->byId[$num_item]["data"].$cdata;
		}
		
    }

	// Procesa las etiquetas de cierre
    protected function _tag_close($parser, $tag) {
    	parent::_tag_close($parser, $tag);
		$this->nivel--;
    }
}
?>