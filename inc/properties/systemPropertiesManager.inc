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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

define ("DOMIT_INCLUDE_DIR",XIMDEX_ROOT_PATH . "/extensions/domit/");

include_once(XIMDEX_ROOT_PATH . "/inc/properties/systemProperties.php");
include_once(DOMIT_INCLUDE_DIR . 'xml_domit_include.php');
include_once(XIMDEX_ROOT_PATH . "/inc/utils.php");
include_once(XIMDEX_ROOT_PATH . "/inc/model/node.php");

class SystemPropertiesManager{
	var $node;
	var $systemProperties;

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
