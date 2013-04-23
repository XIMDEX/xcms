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




include_once(XIMDEX_ROOT_PATH . "/inc/model/node.inc");
include_once(XIMDEX_ROOT_PATH . "/inc/parsers/pvd2rng/PVD2RNG_Filters.class.php");

class PVD2RNG {

	const XMLNS_XIM = 'http://ximdex.com/schema/1.0';

	private $_pvdxpath = null;
	private $_dompvd = null;
	private $_domrng = null;
	private $_rngxpath = null;
	private $_rngElements = null;	// Controla los elementos ya procesados
	private $_filters = null;
	private $_options = null;


	public function __construct($options=null) {
		$this->_options = is_array($options)
			? $options
			: array(
					'return' => 'rng',		// returned element: rng | tree
					'attributes' => true,	// attributes: true | false
					'content' => true		// default content: true | false
					);
	}

	/**
	 * Devuelve el objeto DOMDocument correspondiente al esquema RNG
	 * @return object
	 */
	public function getRNG() {
		return $this->_domrng;
	}

	/**
	 * Obtiene el contenido de una PVD a partir de su idNode.
	 * Si se indica el parametro $node se obtendran sus hojas de estilos dependientes.
	 * @param int idpvd
	 * @param object node
	 * @return boolean
	 */
	public function loadPVD($idpvd) {

		$pvd = new Node($idpvd);
		if (!($pvd->get('IdNode') > 0)) {
			XMD_Log::error('Se esta intentando obtener un nodo que no existe: ' . $idpvd);
			return false;
	    }

	    if ($pvd->getNodeType() != 5045 /*'VisualTemplate'*/) {
			XMD_Log::error('El nodo indicado para la transformacion no es una PVD: ' . $idpvd);
			return false;
	    }

		$content = $pvd->GetContent();
		$content = explode("##########", $content);
		$content = $content[0]; //str_replace("'", "\'", $content[0]);

		unset($this->_dompvd);
		unset($this->_pvdxpath);

		if (empty($content)) {
			return false;
		}
		
		$this->_dompvd = new DOMDocument();
		$result = $doc->loadXML($content);
		if (!$result) {
			return false;
		}
		$this->_pvdxpath = new DOMXPath($this->_dompvd);

		return true;
	}

	/**
	 * Realiza la transformacion de la PVD en dos pasos, primero se obtiene una jerarquia
	 * de elementos con sus correspondientes atributos, despues se forma el esquema RNG.
	 * @return DOMDocument Retorna el documento RNG o false si ocurrio un error.
	 */
	public function transform($filters= null, $options=null) {

		$this->_filters = is_array($filters) ? $filters : PVD2RNG_Filters::getDefaultRules();
		$this->_options = is_array($options) ? $options : $this->_options;

		unset($this->_domrng);
		$this->_domrng = new DOMDocument('1.0', 'UTF-8');
		$this->_rngxpath = new DOMXPath($this->_domrng);

		// Crea una raiz temporal para el documento...
		$start = $this->_domrng->createElement('start');
		$start->setAttribute('xmlns', 'http://relaxng.org/ns/structure/1.0');
		$this->_domrng->appendChild($start);


		if ($this->_dompvd === null) {
			XMD_Log::error('Se debe indicar una PVD valida para transformarla.');
			return false;
		}

		$this->_rngElements = array();

		$docxap = $this->get_template('docxap');
		if ($docxap) {
			$this->parse_template('docxap', $docxap, $start);
		}else{
			XMD_Log::error("La pvd no tiene template docxap, pvd incompatible con rng");
		}


		$f = new PVD2RNG_Filters();
		foreach ($this->_filters as $filter=>$params) {
			$filter = "filter_{$filter}";
			if (method_exists($f, $filter)) {
				$f->$filter($this->_domrng, $params);
			}
		}
//		debug::log($this->_domrng->saveXML());

		if ($this->_options['return'] == 'rng') {
			// Procesa los elementos obtenidos para formar la RNG
			$domrng =& $this->create_rngdom();
			unset($this->_domrng);
			$this->_domrng =& $domrng;
		}

		return $this->_domrng;
	}

	/**
	 * Procesa los elementos obtenidos para formar la RNG
	 * @return object DOMDocument
	 */
	private function & create_rngdom() {

		$domrng = new DOMDocument('1.0', 'UTF-8');

		// --------------------------------------------------------------------------------------
		// Estructura inicial para RelaxNG
		// --------------------------------------------------------------------------------------
		$grammar = $domrng->createElement('grammar');
		$grammar->setAttribute('xmlns', 'http://relaxng.org/ns/structure/1.0');
		$grammar->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
		$grammar->setAttribute('datatypeLibrary', 'http://www.w3.org/2001/XMLSchema-datatypes');
		// --------------------------------------------------------------------------------------

		$start = $domrng->createElement('start');
		$grammar->appendChild($start);

		// Comienza a procesar la raiz del RNG
		$docxap =& $this->_rngElements['docxap'];
		$docxap =& $this->create_rngnode($docxap, $start, $grammar);

		$domrng->appendChild($grammar);
		return $domrng;
	}

	/**
	 * Funcion recursiva
	 * Procesa un unico elemento y lo inserta en el documento RNG.
	 * @param DOMNode node Elemento a procesar
	 * @param DOMNode parent Padre del elemento en el RNG
	 * @param DOMNode grammar Elemento grammar donde insertar los namedPatterns
	 * @return DOMNode
	 */
	private function & create_rngnode($node, &$parent, &$grammar) {

		$null = null;
		if (!($node instanceof DOMElement)) return $null;
		$domrng =& $parent->ownerDocument;

		if ($node->nodeName == 'ref') {
			$ref = $domrng->importNode($node);
			$ref->setAttribute('name', 'def_' . $node->getAttribute('name'));
			$parent->appendChild($ref);
			return $ref;
		}

		$rngName = trim($node->getAttribute('name'));
		if (strlen($rngName) == 0) return $null;

		// Creacion del elemento (nodo RNG)
		$rngNode = $domrng->createElement('element');
		$rngNode->setAttribute('name', $rngName);

		$elemlist = $node->childNodes;
		$attributes = array();
		$elements = array();

		// Procesa todos los hijos y separa los atributos de los elementos
		foreach ($elemlist as $element) {
			if ($element->nodeName == 'attribute') {
				$attributes[] = $element;
			} else {
				$elements[] = $element;
			}
		}

		// En primer lugar se importan los atributos en el rngNode
		foreach ($attributes as $attribute) {
			$attr = $domrng->importNode($attribute, true);
			$rngNode->appendChild($attr);
		}

		// Lo elementos se procesaran recusivamente, se insertan despues de los atributos
		$interleave = $domrng->createElement('interleave');
		foreach ($elements as $element) {
			//En un interleave los elementos de dentro serán, por flexibilidad
			//opcionales
			$optional = $domrng->createElement("optional");
			if ($element->prefix == 'xim') {
				// Los elementos en el espacio de nombres 'ximdex' se insertan tal cual
				$elem = $domrng->importNode($element, true);
				$rngNode->appendChild($elem);
			} else {
				// Los elementos en el espacio de nombres 'relaxng' se procesan
				$elem = $this->create_rngnode($element, $rngNode, $grammar);
				if ($elem !== null) {
					if ($elem->nodeName != 'ref') {
						$elemName = trim($elem->getAttribute('name'));
						if ($elemName != '') {
							$ref = $domrng->createElement('ref');
							$ref->setAttribute('name', 'def_' . $elemName);
							// El rngNode contendra una referencia al namedPattern correspondiente
//							$interleave->appendChild($ref);
							$elem = $ref;
						}
					}
					// El rngNode contendra una referencia al namedPattern correspondiente
					$optional->appendChild($elem);
					$interleave->appendChild($optional);
				}
			}
		}

		// Las PVD no contiene informacion sobre cardinalidad y orden con lo que
		// la RNG resultante sera bastante permisiva.
		if ($interleave->childNodes->length > 0) {
			$zeroOrMore = $domrng->createElement('zeroOrMore');
			$zeroOrMore->appendChild($interleave);
			$rngNode->appendChild($zeroOrMore);
		}

		// Las PVD no contienen informacion acerca del tipo de datos de los elementos.
		// Se asume que es texto ya que si no por defecto sera <empty/>
		$dataType = $domrng->createElement('text');
		$rngNode->appendChild($dataType);
		$parent->appendChild($rngNode);

		// El elemento <docxap/> no tendra su correspondiente namedPattern,
		// se insertara directamente bajo el nodo <start/>
		if ($rngName != 'docxap' && $rngName != '') {
			// Se inserta el namedPattern bajo el elemento <grammar/>
			$define = $domrng->createElement('define');
			$define->setAttribute('name', 'def_' . $rngNode->getAttribute('name'));
			$define->appendChild($rngNode);
			$grammar->appendChild($define);
		}

		return $rngNode;
	}

	private function get_template($name) {

		$template = null;
		$query = "edx:view/edx:template[@name='$name']";

		if (!is_null($this->_pvdxpath)) {
			$nodelist = $this->_pvdxpath->query($query);
			if ($nodelist->length > 0) $template = $nodelist->item(0);
		}

		return $template;
	}

	/**
	 * Procesa un elemento <template/> para obtener el correspondiente elemento del RNG.
	 * @param DOMElement template
	 * @return boolean
	 */
	private function parse_template($name, $template, &$rngParent) {

		if (!$template || !($template instanceof DOMElement)) {
			XMD_Log::error('No se puede parsear. Template no v�lido.');
			return null;
		}

//debug::log(sprintf('> %s :: %s', $rngParent->getAttribute('name'), $name));

		$rngNode = $this->_domrng->createElement('element');
		$rngNode->setAttribute('name', $name);
		$rngParent->appendChild($rngNode);
		$this->_rngElements[$name] = $rngNode;

		if ($name != 'docxap') {
			// Obtiene los atributos del elemento
			if ($this->_options['attributes'] === true) {
				$attributes = $this->parse_attributes($template);
				$this->appendAttributesToElement($rngNode, $attributes);
			}

			if ($this->_options['content'] === true) {
				// Obtencion del contenido por defecto (vista previa por elemento)
				$defaultContent = trim($this->parse_insert($template));
				$this->appendDefaultContent($rngNode, $defaultContent);
			}
		}

		// Se procesan todos los elementos que puedan estar definidos bajo el <template/>
		$referencedElements = $this->parse_elements($template);

		if(is_array($referencedElements) && count($referencedElements) > 0) {
			foreach($referencedElements as $name=>$refElem) {

				if (!isset($this->_rngElements[$name])) {
//debug::log('-------------- Creating: '.$name);
					$node = $this->parse_template($name, $refElem, $rngNode);
				} else {
//debug::log('-------------- Referencing: '.$name);
//					$rngNode->appendChild($this->_rngElements[$name]->cloneNode(true));
//					$ref =& $this->_domrng->createElementNS(self::XMLNS_XIM, 'xim:cyclic');
//					$ref->setAttribute('ref', $name);
					$ref =& $this->_domrng->createElement('ref');
					$ref->setAttribute('name', $name);
					$rngNode->appendChild($ref);
				}
			}
		}

		return $rngNode;
	}

	/**
	 * Se extraen todos los posibles elementos definidos bajo un <template/>
	 * @param DOMELement node
	 * @return array
	 */
	private function parse_elements(&$node) {

		// Las etiquetas <match> definen elementos del esquema.
		// NOTE: Se asume que no se pueden anidar elementos <match> y que siempre contienen un <edxtemplate>.
		// NOTE: Los atributos se definen en las etiquetas <edx:attributes>, hijas de <edx:match>
		// NOTE: Puede haber edxtemplate en vista por defecto.

		$arrayNodeList = array();

		// Se buscan las referencias a elementos bajo la primera de las plantillas de vista
		$query = 'edx:xhtml[1]/descendant::*[@edxtemplate]';
		$elements = $this->_pvdxpath->query($query, $node);
		foreach ($elements as $element) {
			$elemName = $element->getAttribute('edxtemplate');
			if (!strpos($elemName, ':')) {
				$arrayNodeList[$elemName] = $element;
			}
		}


		// Parseo de los elementos definidos v�a edx:match
		$nodelist = $this->_pvdxpath->query('edx:match', $node);
		foreach($nodelist as $match) {
			$elemName = $match->getAttribute('element');
			$arrayNodeList[$elemName] = $match;
		}

		$ret = array();

		// Se crean los elementos RNG y sus correspondientes atributos
		foreach ($arrayNodeList as $element) {

			if ($element->nodeName == 'edx:match') {
				$name =$element->getAttribute('element');
				$query = '*[@edxtemplate]';
				$nodelist = $this->_pvdxpath->query($query, $element);
				$node = $nodelist->item(0);
				$templateName = $node->getAttribute('edxtemplate');
			} else {
				$name = $element->getAttribute('edxtemplate');
				$templateName = $name;
			}

			$template = $this->get_template($templateName);
			if (!strpos($templateName, ':') && $template) {
				$ret[$name] = $template;
			}
		}

		return $ret;
	}

	/**
	 * Procesa los atributos de un elemento
	 * @param DOMElement node
	 * @return array
	 */
	private function parse_attributes(&$node) {

		$arrAttributes = array();
		$nodeName = $node->getAttribute('name');

		// Atributos definidos en las plantillas de vista (elementos widget)
		$query = 'edx:view/edx:template[@name="%s"]/edx:xhtml/descendant::*[@edxtemplate="widget:text" or @edxtemplate="widget:select"]';
		$query = sprintf($query, $nodeName);
		$attributes = $this->_pvdxpath->query($query);

		foreach ($attributes as $attribute) {

			$attrName = $attribute->getAttribute('edxtemplate');

			// Ensure that this is a real attribute, not a XPath element reference
			$edxpath = $attribute->getAttribute('edxpath');
			$query = str_replace("{$nodeName}/@@", "{$nodeName}/@", sprintf("//%s/@%s\n", $nodeName, $edxpath));
			$nodelist = $this->_pvdxpath->query($query, $node);

			if ($nodelist->length > 0) {
				$type = explode(':', $attrName);
				$type = $type[1];
				$method = 'parse_attribute_' . $type;
				$arrAttributes[] = $this->$method($attribute, true);
			}
		}

		// Atributos definidos en los elementos <edx:match/>
		$query = '//edx:match[@element="%s"]/edx:attributes/edx:attribute';
		$query = sprintf($query, $nodeName);
		$attributes = $this->_pvdxpath->query($query, $node);

		foreach ($attributes as $attribute) {
			$attrName = $attribute->getAttribute('name');
			if ($attrName != '.') {
				$value = $attribute->nodeValue;

				if (strlen($value) == 0) {
					// Text type
					$attr = $this->parse_attribute_text($attribute);
				} else {
					// Select type
					$attr = $this->parse_attribute_select($attribute);
				}

				if ($attr !== null) {
					$arrAttributes[] = $attr;
				}
			}
		}

		return $arrAttributes;
	}

	/**
	 * Procesa un atributo de tipo texto
	 */
	private function parse_attribute_text($node, $edxPathMode = false) {
		$name = ($edxPathMode) ? str_replace('@', '', $node->getAttribute('edxpath')) : $node->getAttribute('name');
		$domelem = $this->_domrng->createElement('attribute');
		$domelem->setAttribute('name', $name);

		$renderizable = $node->getAttribute('renderElement');

		if (!empty($renderizable)) {
			$attribute = $this->_domrng->createElementNS(self::XMLNS_XIM, 'xim:attribute');
			$attribute->setAttribute('renderElement', $renderizable);
			$attribute->setAttribute('renderLabel', $node->getAttribute('renderLabel'));
			$domelem->appendChild($attribute);
		}

		return $domelem;
	}

	/**
	 * Procesa un atributo de tipo select junto con sus valores por defecto
	 */
	private function parse_attribute_select($node, $edxPathMode = false) {

		$name = ($edxPathMode) ? str_replace('@', '', $node->getAttribute('edxpath')) : $node->getAttribute('name');
		$domelem = $this->_domrng->createElement('attribute');
		$domelem->setAttribute('name', $name);

		$renderizable = $node->getAttribute('renderElement');

		if (!empty($renderizable)) {
			$attribute = $this->_domrng->createElementNS(self::XMLNS_XIM, 'xim:attribute');
			$attribute->setAttribute('renderElement', $renderizable);
			$attribute->setAttribute('renderLabel', $node->getAttribute('renderLabel'));
			$domelem->appendChild($attribute);
		}

		$choice = $this->_domrng->createElement('choice');
		$optionTagName = ($edxPathMode) ? 'option' : 'edx:value';
		$options = $this->_pvdxpath->query($optionTagName, $node);

		foreach ($options as $option) {
			$value = ($edxPathMode) ? $option->getAttribute('value') : $option->nodeValue;
			$type = ($edxPathMode) ? 'string' : $option->getAttribute('type');
			$domopt = $this->_domrng->createElement('value', $value);
			$domopt->setAttribute('type', $type);
			$choice->appendChild($domopt);
		}

		$domelem->appendChild($choice);
		return $domelem;
	}

	/**
	 * Parse a <edx:insert/> section, this is the default content for an element.
	 */
	private function parse_insert($template) {

		$defaultContent = "";

		$templateName = $template->getAttribute('name');
		$query = 'edx:insert';
		$insert = $this->_pvdxpath->query($query, $template);

		// Finding all of grandchildren text nodes.
		foreach ($insert as $content) {
			$childs = $content->childNodes;
			foreach ($childs as $child) {
				if ($child->nodeType == 1) {
					$chs = $child->childNodes;
					foreach ($chs as $ch) {
						if($ch->nodeType == 3) {
							$defaultContent .= $this->_dompvd->saveXML($ch) . "\n";
						}
					}
				}
			}
		}

		return $defaultContent;
	}

	private function appendAttributesToElement(&$domelem, $attributes) {

		$containsUidAttribute = false;
		$addedAttributes = array();

		foreach ($attributes as $attribute) {
			$attrName = $attribute->getAttribute('name');
			if (!isset($addedAttributes[$attrName])) {
				$domelem->appendChild($attribute);
				$addedAttributes[$attrName] = true;
			}
			if ($attrName == 'uid') $containsUidAttribute = true;
		}

		if (!$containsUidAttribute) {
			$uidAttribute = $this->_domrng->createElement('attribute');
			$uidAttribute->setAttribute('name', 'uid');
			$domelem->appendChild($uidAttribute);
		}
	}

	private function appendDefaultContent(&$domelem, $defaultContent) {

		// NOTE: Anotaciones son elementos en un espacio de nombre distinto al de RNG.
		// En este caso se usa para indicar cual es el contenido por defecto de un nuevo elemento.

		if (strlen($defaultContent) > 0) {
			$annotation = $this->_domrng->createElementNS(self::XMLNS_XIM, 'xim:default_content');
			$content = $this->_domrng->createTextNode($defaultContent);
			$annotation->appendChild($content);
			$domelem->appendChild($annotation);
		}
	}

}

?>
