<?php
 
/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/


if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

//Extensions: The location of the RAP classes should be specified. The name of the var RDFAPI_INCLUDE_DIR cannot be changed. It is defined by RAP. 
//define("RDFAPI_INCLUDE_DIR", XIMDEX_ROOT_PATH . "/extensions/rdfapi-php/api/");

define ("DOMIT_INCLUDE_DIR",XIMDEX_ROOT_PATH . "/extensions/domit/");

//include(RDFAPI_INCLUDE_DIR."RdfAPI.php");
//include(RDFAPI_INCLUDE_DIR."vocabulary/RDF_C.php");
//include(RDFAPI_INCLUDE_DIR."vocabulary/RDFS_C.php");
//include(RDFAPI_INCLUDE_DIR."syntax/RdfSerializer.php");
#This is the namespace of the schema (RDF) defined by Ximdex.
#In the schema concepts such as 'Node', 'View',..., are represented
#(note: Think how to put this namespace out from here)

//define("NAMESPACE", "http://www.ximdex.com/project/docedu#");
include_once(XIMDEX_ROOT_PATH . "/inc/properties/systemProperties.php");
//Domit
include_once(DOMIT_INCLUDE_DIR . 'xml_domit_include.php');
//Ximdex space
include_once(XIMDEX_ROOT_PATH . "/inc/utils.php");
include_once(XIMDEX_ROOT_PATH . "/inc/model/node.inc");

class SystemPropertiesManager{
	var $node; //node ximdex from whom we are going to ask for properties
	var $systemProperties;
	//var $ontoSchema;//RDF schema where the different properties e are going to evaluate are kept 	
	//var $tieneNombre;
	//var $tieneDescripcion;
	//var $tieneTipo;

	/**
	 * SystemProperties class construct
	 */
	function SystemPropertiesManager($nodeID = null){
		//This line should be commented while RDF scheme is not in use
		//$this->initSchema();
		$this->node = New Node($nodeID);
		$this->systemProperties = new SystemProperties(); 
	}
	
	/**
	 * Function which allows to modify the node we're quering
	 */
	function setNode($nodeID=null){
		$this->node->SetID($nodeID);
	}
	
	/**
	 * Function which returns a string RDF/XML with the properties which are common for all ximdex nodes. These are fixed
	 */
	/*function getSystemPropertiesRDF(){
		//Creating a temporal model associated to the ximdex node we are working with
		$onto = ModelFactory::getOntModel(MEMMODEL, RDFS_VOCABULARY);
		//$onto->addModel($this->ontoSchema);
		$nodo_instance = $onto->createIndividual(NAMESPACE."nodo_".$this->node->GetID());
		//Adding values to the node properties
		$nodo_instance->addProperty($this->tieneNombre,$onto->createLiteral($this->node->GetNodeName()));
		$nodo_instance->addProperty($this->tieneDescripcion, $onto->createLiteral($this->node->GetDescription()));
		$nodo_instance->addProperty($this->tieneTipo, $onto->createLiteral($this->getNodeType()));
		$model = $onto->getModel();
		$ser = new RdfSerializer();
		return $ser->serialize($model,null,"UTF-8");
	}*/
	
	/**
	 * Function which inits the RDF schema with all the node properties
	 */
	/*function initSchema(){
		//Creating the model where classes, properties, ..., are going to be defined	
    	$this->ontoSchema = ModelFactory::getOntModel(MEMMODEL, RDFS_VOCABULARY);
    	
    	//It represents the class "nodo" defined in the ontology created by Ximdex	
        $ontNodo  = $this->ontoSchema->createOntClass(NAMESPACE.'NodoXimdeX');
        
        //Adding the properties we are going to use in the schema.
		$this->tieneNombre = $this->ontoSchema->createOntProperty(NAMESPACE.'hasName');
		$this->tieneDescripcion = $this->ontoSchema->createOntProperty(NAMESPACE.'hasDescription');
		$this->tieneTipo = $this->ontoSchema->createOntProperty(NAMESPACE.'hasNodeType');
	}*/

	/**
	 * TODO.
	 */
	function getSystemPropertiesXML(){
		//array with system properties
		$properties = $this->systemProperties->getSystemProperties();
		$documentProperties =& new DOMIT_Document();
		//'&' is used due to domit compatiblity with php4  
		$root =& $documentProperties->createElement('nodoXimDEX');
		$root->setAttribute("name",$this->getProperty($this->systemProperties->getSystemProperty("NAME")));
		$documentProperties->appendChild($root);

		//$child;
		//$childValue;
		
		while(list($key, $arr) = each($properties)){
			$child =& $documentProperties->createElement($arr[0]);
			$child->setAttribute("editable",$arr[1]);
			$childValue =& $documentProperties->createTextNode($this->getProperty($arr[0]));
			$child->appendChild($childValue);
			$root->appendChild($child);
		}
		return $documentProperties->toString(false,false);
	}
	
	/**
	 * TODO
	 */
	function getProperty($propertyName){
		switch($propertyName){
			case $this->systemProperties->getSystemProperty("NAME"):
				return $this->getNodeName();
			case $this->systemProperties->getSystemProperty("DESCRIPTION"):
				return $this->getNodeDescription();
			case $this->systemProperties->getSystemProperty("NODETYPE"):
				return $this->getNodeType();
			case $this->systemProperties->getSystemProperty("NODEID"):
				return $this->getNodeID();
		}
	}
	
	/**
	 * TODO
	 */
	function getNodeType(){
		$id = $this->node->GetNodeType();
		$nodetype=new NodeType($id);
	    return $nodetype->GetName();
	}
	
	/**
	 * TODO
	 */
	function getNodeID(){
		return $this->node->GetID();
	}
	
	/**
	 * TODO
	 */
	function getNodeDescription(){
		return $this->node->GetDescription();
	}
	
	/**
	 * TODO
	 */
	function getNodeName(){
		return $this->node->GetNodeName();
	}
}
?>
