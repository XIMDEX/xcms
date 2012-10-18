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

 *                                                                            *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/utils.inc');
ModulesManager::file('/actions/deletenode/baseIO.php');
ModulesManager::file('/inc/helper/String.class.php');

class BorrarNodos{

var $xml_str;
var $idXimlet;

function BorrarNodos($str_xml){
	$this->str_xml = $str_xml;
	$this->removeTags('deletenode');
	$this->removeTags('respuesta');
}

//Method which creates a xml with node dependencies
function getDependencies($userID){
	$user = new User($userID);
	$canDeleteOnCascade = $user->HasPermission("delete on cascade");
	$str_xml = "<respuesta>";
	if($canDeleteOnCascade){
		$str_xml .= "<permisos valor='true' />";
	}else{
		$str_xml .= "<permisos valor='false' />";
	}
	$arr_nodos = $this->getNodosFromXML('nodo','id');
	$arr_nodos_nombre = $this->getNodosFromXML('nodo','name');
	$j = 0;
	foreach($arr_nodos as $idNode){
		$arr_dependencias = baseIO_listadoDependencias($idNode);
		if (is_array($arr_dependencias)){
			$limite = count($arr_dependencias);
			$str_xml .= "<dependencia name = '".$arr_dependencias[0]["Padre"]."'>";
			for($i=0;$i<$limite;$i++) {
				if ($arr_dependencias[$i]["Id"] != null){
					$str_xml .= "<nodo id='".$arr_dependencias[$i]["Id"]."' name='".$arr_dependencias[$i]["Name"]."'  />";
				}
			}
			$str_xml .= "</dependencia>";
		}else{
			$str_xml .= "<error name='$arr_nodos_nombre[$j]' mensaje='"._("Node does not exist. Update XimSearch.")."' />";

		}
	$j++;
	}
	$str_xml .= "</respuesta>";
	return $str_xml;
}
function GetNodeInformation($arr_nodos){

	foreach($arr_nodos as $idNodo){
		$node = new node($idNodo);
		$arr_nodos_info[] = $node->GetNodeName();
	}
return $arr_nodos_info;
}
//Méthod which associates selected sections to selected ximlet
function DeleteNode($userid){

	$arr_nodos = $this->getNodosFromXML('nodo','id');
	$arr_nodos_info = $this->GetNodeInformation($arr_nodos);
	foreach($arr_nodos as $idNodo){
		$node = new node($idNodo);
		$arr_resultado[] = baseIO_borradoNodos($idNodo,$userid);
	}
	$resultado = $this->XmlResultado($arr_nodos_info,$arr_resultado);
	return $resultado;

}

function XmlResultado($arr_nodos,$arr_resultado){

	$limite = count($arr_nodos);
	$xml_final = "<respuesta><nodos>";
	for($i=0;$i<$limite;$i++)
	{
		if ($arr_resultado[$i] ==='0'){
			$xml_final .= "<nodocorrecto name='".$arr_nodos[$i]."' />";
		}else{
			$xml_final .= "<nodoincorrecto name='".$arr_nodos[$i]."' error='$arr_resultado[$i]' />";
		}
		unset($node);
	}
	$xml_final .= "</nodos></respuesta>";
	return $xml_final;
}

function removeTags($tag_name){

	$tag_ini = '<' . $tag_name . '>';
	$tag_fin = '</' . $tag_name . '>';
	$this->str_xml = str_replace ($tag_ini, '', $this->str_xml);
	$this->str_xml = str_replace ($tag_fin, '', $this->str_xml);

}

//Saves into an array content of xml tags which are parameters
function getNodosFromXML($tag_name){

	$this->str_xml = $this->getXMLFinal();

	$domDoc = new DOMDocument();
	$domDoc->validateOnParse = true;
	$domDoc->preserveWhiteSpace = false;
	$domDoc->loadXML($this->str_xml);
	$arrayOrden = $domDoc->getElementsByTagname($tag_name);
	$limite = count($arrayOrden);

	for ($i=0;$i<$limite;$i++)
	{
		$arr_ids[$i] = $arrayOrden[$i]->get_attribute("id");
	}
	//At firts it adds returned node to end and beginning of xml to avoid it does not show an error when object XML is created
	// and it delete it because it will be added more objects to XML string
	$this->removeTags('respuesta');
	return $arr_ids;
}

function getXMLFinal(){

	 $resultado = "<respuesta>" . String::stripslashes($this->str_xml) . "</respuesta>";
	 return $resultado;
}


}
?>
