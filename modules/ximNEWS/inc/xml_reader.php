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

ModulesManager::file('/extensions/domit/xml_domit_include.php');
ModulesManager::file('/extensions/domit/xml_domit_shared.php');
ModulesManager::file('/extensions/domit/xml_domit_parser.php');
ModulesManager::file('/extensions/domit/xml_domit_utilities.php');
ModulesManager::file('/extensions/domit/xml_domit_getelementsbypath.php');
ModulesManager::file('/extensions/domit/xml_domit_nodemaps.php');
ModulesManager::file('/extensions/domit/xml_domit_cache.php');
ModulesManager::file('/extensions/domit/xml_saxy_parser.php');
ModulesManager::file('/extensions/domit/xml_domit_doctor.php');
ModulesManager::file('/extensions/domit/php_file_utilities.php');



/**
*   @deprecated Pending of the removal of the old browser.
*   @brief Deprecated: pending of the removal of the old browser.
*	
*	Triggers the removal of Domit.
*/

class xml_reader
{
var $doc;
var $root;

function xml_reader()
{
}
function set_file($path)
{
	$this->doc = & new DOMIT_Document();
     	$success = $this->doc ->loadXML($path);
	$this->root = & $this->doc->documentElement;
}
function set_xmlString($content)
{
	$this->doc = & new DOMIT_Document();
	$this->doc->parseXML($content,true);
	$this->root = & $this->doc->documentElement;
}

function getDataList($from,$name,$value)
{
	$dbObj = new DB();
	$resultado = array();
	$sql = "SELECT " . $name . ", ".$value. " FROM " . $from;
	$dbObj->Query($sql);
	while (!$dbObj->EOF) { 	
		$index = $dbObj->GetValue($name);
		$resultado[$index] = $dbObj->GetValue($value);
		$dbObj->Next();
	}       
	return $resultado;
}

//Grupo de elementos, con posibles opciones.
function get_group($tag)
{
	$result = array();
	$grupo = & $this->root->getElementsByTagName($tag);
	$grupo = $grupo->item(0);
	
        $elements = & $grupo->childNodes;
	$num_elements = $grupo->childCount;
	
	for($n=0; $n < $num_elements; $n++){
		$data = array();
		$element = $elements[$n];
		$attrs = $element->attributes->arNodeMap;
                foreach($attrs as $attr){
			$name = $attr->getName();
			$data[$name] = $element->getAttribute($name);
		}
		if($element->hasChildNodes()){
			$childs = & $element->childNodes;
			$num_childs = $element->childCount;
			for($c=0; $c < $num_childs; $c++){
				$child = $childs[$c];
				if($child->nodeName == "option"){
					$option = array();
					$name = $child->getAttribute("name");
					$value = $child->getAttribute("value");
					$option[$name] = $value; 
					if(!$data["options"]){
						$data["options"] = array();
					}
					$data["options"][$name] = $value;
				}
				else if($child->nodeName == "options"){
					$from = $child->getAttribute("from");
					$name = $child->getAttribute("name");
					$value = $child->getAttribute("value");
					$option = $this->getDataList($from,$name,$value);
					if(!$data["options"]){
						$data["options"] = array();
					}
					foreach($option as $name=>$value){
						$data["options"][$name] = $value;
					}
				}
			}
		}
		$result[] = $data;
	}
	
	return $result;
}
function get_bulletin_info()
{
	$info = array();
	$elements = & $this->root->firstChild ->childNodes;
	$num_elements = $this->root->firstChild->childCount;
	for($n=0; $n < $num_elements; $n++){
             $element = $elements[$n];
	     if($element->hasAttribute("visor")){
	        $index = $element->getAttribute("visor");
		$info[$index] = $element->getText();
	     }	
        }
	return $info;
}
function get_bulletin_info2()
{
	$info = array();
	$elements = & $this->root->firstChild ->childNodes;
	$num_elements = $this->root->firstChild->childCount;
	for($n=0; $n < $num_elements; $n++){
             $element = $elements[$n];
             $index = $element->getAttribute("label");
	     $info[$index] = $element->getText();	
        }
	return $info;
}

function get_bulletin_header()
{
        $header = array();
	$elements = & $this->root->childNodes;
	$num_elements = $this->root->childCount;
	for($n=0; $n < $num_elements; $n++){
             $element = $elements[$n];
	     if($element->hasAttribute("visor")){
	        $index = $element->getAttribute("visor");
		$header[$index] = $element->getAttribute("label");
	     }	
        }
	return $header;
}
function filter_pvd($nodeID)
{     
    	$strDoc = new StructuredDocument($nodeID);
    	$templateID = $strDoc->GetDocumentType();
    	$templateNode = new Node($templateID);
 	$templateContent = $templateNode ->class-> GetContent();
	$templateContent = split("##########",$templateContent);
	$content = str_replace("'", "\'", $templateContent[1]);
    	$templateNew =& new DOMIT_Document();
    	$success = $templateNew->parseXML($content,true);
	$this->root  = & $templateNew->documentElement->firstChild;
}

}
?>
