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

ModulesManager::file('/inc/utils.php');
ModulesManager::file('/actions/movenode/baseIO.php');


class MoverNodo{

var $xml_str;
var $idXimlet;

function MoverNodo($str_xml){
	$this->str_xml = $str_xml;
	$this->removeTags('movenode');
	$this->removeTags('respuesta');
}

//Method which changes destination nodes
function MoveNode(){
	
	$arr_nodos = $this->getNodosFromXML('nodo','id');
	$arr_destino = $this->getNodosFromXML('destino','id');
	foreach($arr_destino as $destinoID){
		foreach($arr_nodos as $nodeID){
			$arr_movenode[] = baseIO_MoverNodos($nodeID, $destinoID);
		}
	}
	array_multisort($arr_movenode,$arr_nodos);
	$resultado = $this->XmlResultado($arr_movenode,$arr_nodos,$arr_destino);
	return $resultado;
	
}
function ObtenerNodoPadre(){
	$arr_tiponodo = $this->getNodosFromXML('tiponodo','id');
	$resultado = "<respuesta><nodospadre>";
	foreach($arr_tiponodo as $id){
		$node = new Node($id);
		$parent = new Node();
		$parent->SetID($node->GetParent());
		$resultado .= "<nodopadre id='".$parent->nodeType->GetName()."' />";
	}
	$resultado .= "</nodospadre></respuesta>";

	return $resultado; 
}
function XmlResultado($arr_movenode,$arr_nodos,$arr_destino){
	$xml_final = "<respuesta><nodos>";
	$arr_nombres = $this->getNodosFromXML('nodo','name');
	$limite =  count($arr_nodos);
	for ($i=0;$i<$limite;$i++)
	{
		//$node = new node($id);
		$node = new node($arr_nodos[$i]);
		if($node->GetID()==NULL){
			$xml_final .= "<error name='$arr_nombres[$i]' mensaje='Nodo no existe. Actualice el XimSearch.' />";
		}
		else{
			$nombre = $node->GetNodeName();
			if ($arr_movenode[$i]==0){
					$xml_final .= "<nodocorrecto name='$nombre'/>";
			}
			else{
				$xml_final .= "<nodoincorrecto name='$nombre'/>";
			}
		}
		unset($node);
	}
	$xml_final .= "</nodos>";
	$xml_final .= "<destinos>";
	foreach($arr_destino as $id)
	{
		$node = new node($id);
		$ruta = $node->GetPath();
		$id = $node->GetID();
		$xml_final .= "<destino id='$id' path='$ruta' />";
		unset($node);
	}
	$xml_final .= "</destinos></respuesta>";
	return $xml_final;
}

function removeTags($tag_name){

	$tag_ini = '<' . $tag_name . '>';
	$tag_fin = '</' . $tag_name . '>';
	$this->str_xml = str_replace ($tag_ini, '', $this->str_xml);
	$this->str_xml = str_replace ($tag_fin, '', $this->str_xml);

}

//Save in an array content of xml tags which are put by parameter
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
	//At first adds "answer" node at beginning and at the end of xml to avoid error when object XML is created
	// and then it is deleted because more things are going to be added to XML string
	$this->removeTags('respuesta');
	return $arr_ids;
}

function getXMLFinal(){

	 $resultado = "<respuesta>" . \Ximdex\Utils\String::stripslashes( $this->str_xml) . "</respuesta>";
	 return $resultado;
}


}