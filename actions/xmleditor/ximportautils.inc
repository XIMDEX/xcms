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
 * @desc Clase de Utilidades de ximPORTA                                      *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/actions/xmleditor/ximportautils.inc');
?>


class ximportautils
{
	var $dbObj;

	function ximportautils()
		{
		$this->dbObj = new DB();
	
	}
	function sacaUrls($dom,$arr_res,$indice) {
	
		if ($dom!=null) {
			//si el nodo no es nulo
			
			$arr_hijos=$dom->child_nodes();
			//sacamos sus hijos y los recorremos...
			
			for($i=0;$i<count($arr_hijos);$i++) {
				//para cada hijo, miramos a ver si es un tag enlace
				if ($arr_hijos[$i]->tagname=="enlace") {
					//si es enlace, añadimos su url, al array resultado, incrementamos el índice del array y seguimos con los otros hijos
					
					$arr_res[$indice]=$arr_hijos[$i]->get_attribute("url");
					$indice++;
				}
				else {
					// si no es un tag enlace, recorremos el nodo en profundidad. Llamamos recursivamente a la función, pasandole tanto el array de resultado, 
					// como el índice por referencia, para que los actualice.
					$this->sacaUrls($arr_hijos[$i],&$arr_res,&$indice);
				}
			}
			// una vez recorrido todo el árbol DOM, devolvemos el array resultado, que se habrá ido llenando en cada llamada recursiva.
		
			return $arr_res;
		}
		
	}
	
	function CompruebaURL($url,$idproject) {
		
		
		$sql="SELECT * FROM Links where url = '".$url."'";
		
	
		$this->dbObj->Query($sql);
		
		if ($this->dbObj->numRows==0) {
			return 0;
		}
		
		if (!$this->dbObj->numErr)
			{
			
			$i = 0;
			while(!$this->dbObj->EOF)
			{
			$array_idlink[$i]=$this->dbObj->GetValue("IdLink");
			$this->dbObj->Next();
			$i++;
			
			}
		}
		
		else {
		$this->SetError(4);
	
				return 0;
		}

		$idlink=0;		
		for($j=0;$j<count($array_idlink);$j++) {
			$node_link=new Node($array_idlink[$j]);
			$idproject_link=$node_link->GetProject();
			if ($idproject==$idproject_link) {
				$idlink=$array_idlink[$j];
			}
		}
		
		return $idlink;
	
	}
	
	function InsertarLink($url,$nodeID,$nodeXimFolder) {

		$nodeType = new NodeType();
		$nodeType->SetByName('Link');
		
		if ($nodeXimFolder!=null){
			$idpadre=$nodeXimFolder;
			}
		else{
			$idpadre=$this->SacaNodoXimPORTA($nodeID);
			}
		$link = new Node();

		
		if ($idpadre!=0) {
			$url_nombre=str_replace('&','@',$url);
			$link->CreateNode($url_nombre,$idpadre, $nodeType->GetID(), null, $url, $description);
			$link->GetID()			;
			return($link->GetID());
		}
		else {
			return 0;
		}	

	
	}
		
	function SacaNodoXimPORTA($nodeID) {
	
		$node=new Node($nodeID);
		$project=$node->GetProject();
		
		$sql="SELECT * FROM Nodes where Name = 'ximPORTA'";
			
		$this->dbObj->Query($sql);
		
		if ($this->dbObj->numRows==0) {
			return 0;
		}
		
		if (!$this->dbObj->numErr)
			{
			
			$i = 0;
			while(!$this->dbObj->EOF)
				{
				$node_ximporta= new Node($this->dbObj->GetValue('IdNode'));
				$parent_node_id=$node_ximporta->GetParent();
				$parent_node=new Node($parent_node_id);
				if ((($node_ximporta->GetProject())==$project)&&($parent_node->GetNodeName()=="ximlink")) {
					return $this->dbObj->GetValue('IdNode');
				}
				$this->dbObj->Next();
			}
			//si llegamos hasta aquí, es porque no ha encontrado nada, con lo que insertamos el nodo ximPORTA.

			//a partir del proyecto, sacando sus hijos, tenemos el nodeid de ximlink
			$node_project=new Node($project);
			$idpadre=$node_project->GetChildByName('ximlink');
			
			//Creamos el nodo ximPORTA con todos los datos necesarios
			$nodeType=new NodeType();
			$nodeType->SetByName('ximPORTA');
			$new_node=new Node();
			$new_node->CreateNode('ximPORTA',$idpadre, $nodeType->GetID(), null);
			return ($new_node->GetID());
	
		}
		
		else {
		$this->SetError(4);
	
				return 0;
		}
	
	
	}
		
	function InsertaEnlacesXml($xml, $nodeID, $nodeXimFolder) {

			
			// Añadimos esta cabecera para que funcione correctamente el DOM
			$header = '<?xml version="1.0" encoding="iso-8859-1"?>';
			$xml = $header.$xml;
	
			// obtenemos el árbol DOM

			$domDoc = new DOMDocument(); 
			$domDoc->validateOnParse = true; 
			$domDoc->preserveWhiteSpace = false; 
			$domDoc->loadXml($xml); 
			$raiz = $domDoc->documentElement; 

			$node=new Node($nodeID);
			$nodeX=new Node($nodeXimFolder);
			$idproject=$node->GetProject();
			
			//Llamamos a la función recursiva sacaUrls, que nos devolverá un array con las URLs del xml. Se le pasa un array vacio por REFERENCIA, y el índice inicial.
			$this->sacaUrls($raiz,&$array_urls,0);
			
			//en arr_urls tenemos las urls que hay que meter..			

			for($i=0;$i<count($array_urls);$i++) {
			//Primero comprobamos si las urls ya están metidas en la db...


				$idlink=$this->CompruebaURL($array_urls[$i],$idproject);
				
				if ($idlink==0) {
					//si devuelve 0, es que la url no está insertada, con lo cual la insertamos..
					$idlink=$this->InsertarLink($array_urls[$i],$nodeID,$nodeXimFolder);
				}

				$url=str_replace('"','',$array_urls[$i]);
			
				
				//Estos caracteres no funcionan en las funciones PHP de String
				
				$xml=str_replace('&amp;','@@',$xml);
				$url=str_replace('&','@@',$url);			
				$xml=str_replace('?','-#-',$xml);
				$url=str_replace('?','-#-',$url);		
				
				$xml=ereg_replace( 'url="'.$url.'"', 'a_enlaceid_url="'.$idlink.'"', $xml);
				
				$xml=str_replace('@@','&amp;',$xml);
				$xml=str_replace('-#-','?',$xml);
				
				
			}
			// Quitamos la cabecera
			$xml = ereg_replace( "<\?.*\?>", "", $xml);
			return $xml;
	}
	

}
?>
