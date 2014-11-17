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
ModulesManager::file('/inc/model/XimNewsAreas.php', 'ximNEWS');
ModulesManager::file('/actions/addtoarea/baseIO.php', 'ximNEWS');
ModulesManager::file('/inc/helper/String.class.php');


class asociacionAreas{

var $xml_str;
var $areas_xml;

function asociacionAreas($str_xml){

	$this->str_xml = $str_xml;
	$this->removeTags('addtoarea');
	$this->removeTags('respuesta');

}

//Method wich create the xml with the news just associated to the areas, and the ones already associated
function AddNewsToAreas(){

	$arr_news = $this->getNodosFromXML('nodo');
	$arr_areas = $this->getNodosFromXML('area');

	$xml_correctos = "<nodoscorrectos>";
	$xml_incorrectos = "<nodosincorrectos>";

	$arr_asociacion = AddNewsToAreas($arr_areas,$arr_news);

	$arr_areas_correctas=array_unique($arr_asociacion["areas_correctas"]);
	$arr_areas_incorrectas=array_unique($arr_asociacion["areas_incorrectas"]);

	$arr_correctos=$arr_asociacion["correctos"];

	foreach($arr_areas_correctas as $areaID) {
	
		$arr_res=array_search($areaID,$arr_correctos);
		
	
	}
	foreach($arr_asociacion as $estado => $arr_resultado)
	{
		$arr_correctos=$arr_resultado["correctos"];
		$arr_incorrectos=$arr_resultado["incorrectos"];
		
		foreach($arr_resultado as $key => $value)
		{
			$idNews = $arr_resultado[$key]["id_news"];
			$nombreNews = utf8_encode($arr_resultado[$key]["name_news"]);
			$idColector = $arr_resultado[$key]["id_area"];
			$nombreColector = utf8_encode($arr_resultado[$key]["name_area"]);

			if ($estado == 'correctos')
			{
				$xml_correctos .= "<nodocorrecto id='".$idNews."' name='".$nombreNews."' idarea='".$idColector."' namearea='".$nombreColector."' />";
			}
			else
			{
				$xml_incorrectos .= "<nodoincorrecto id='".$idNews."' name='".$nombreNews."' idarea='".$idColector."' namearea='".$nombreColector."' />";
			}
		}//cierre foreach $arr_resultado
	}//cierre foreach $arr_asociacion

	$xml_correctos .= "</nodoscorrectos>";
	$xml_incorrectos .= "</nodosincorrectos>";
	$this->str_xml .= $xml_correctos . $xml_incorrectos;

	return $this->getXMLFinal();
}

//Method which obtain the collectors of the sections passed as param
function getAreas($nodeID){

	$obj_areas = new XimNewsAreas();
	$arr_areas = $obj_areas->GetAllAreas();
	
//	$obj_menu_categorias = new menu_categories();
//	$this->str_xml .= $obj_menu_categorias->mostrar_categorias_xml($arr_areas);
// workaround, a file had to be deleted and this was its last reference.
	$this->str_xml .= '<table width="100%">';
	foreach($arr_areas as $catID => $catName){
		$this->str_xml .= "<tr>";
		$this->str_xml .= "<td class='filaclara' ";
		$this->str_xml .= "onmouseover='cambiar_color_over(this)' ";
		$this->str_xml .= "onmouseout='cambiar_color_out(this)' >";
		$this->str_xml .= "<span id='". $catID ."' style='cursor:hand;' onclick='show_bulletins(this);'>";
		$this->str_xml .= $catName;
		$this->str_xml .= "</span>";
		$this->str_xml .= "</td>";
		$this->str_xml .= "</tr>";
	}
	$this->str_xml .= "</table>";

	$resultado = $this->getXMLFinal();
	return String::stripslashes($resultado);

}

//Method which delete the label passed as param, both opening and closing
function removeTags($tag_name){

	$tag_ini = '<' . $tag_name . '>';
	$tag_fin = '</' . $tag_name . '>';
	$this->str_xml = str_replace ($tag_ini, '', $this->str_xml);
	$this->str_xml = str_replace ($tag_fin, '', $this->str_xml);

}

//Method which add opening and closing labels to xml
function getXMLFinal(){
	 $resultado = "<respuesta>" . String::stripslashes($this->str_xml) . "</respuesta>";
	 return $resultado;
}

//Method which save in a array the xml label content passed by param
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
	//At the beginning, adding the answer nodo at the ginning and end of the xml
  // in order to avoid an error creating the XML object
  // and the it is deleted as more things are going to be added to the XML string
	$this->removeTags('respuesta');
	return $arr_ids;
}

}
?>
