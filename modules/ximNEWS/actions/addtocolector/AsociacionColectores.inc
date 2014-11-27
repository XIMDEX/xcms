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


ModulesManager::file('/actions/addtocolector/baseIO.php', 'ximNEWS');
ModulesManager::file('/inc/menu_colector.inc', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');

class asociacionColectores{

var $xml_str;
var $colectores_xml;

function asociacionColectores($str_xml){

	$this->str_xml = $str_xml;
	$this->removeTags('addtocolector');

}

//Method which create the xml with the news that have been associated to collectors and the ones already associated
function AddNewsToColectors(){
	$arr_news = $this->getNodosFromXML('nodo');
	$arr_colectores = $this->getNodosFromXML('colector');
	$xml_correctos = "<nodoscorrectos>";
	$xml_incorrectos = "<nodosincorrectos>";
	$fechaInicio = $this->getAtributosEspeciales('fecha_inicio');
	$fechaFin = $this->getAtributosEspeciales('fecha_fin');
	$versiones = $this->getAtributosEspeciales('version');
	//If $versiones is 0 we obtain an array with the news last versions
  //and if it is 1, we obtain the last versions from collectors

	if ($versiones == 0){
		$version = $this->getVersionNews($arr_colectores,$arr_news);
	}
	elseif ($versiones == 1){
		$version = $this->getVersionColector($arr_colectores,$arr_news);
	}
	$arr_asociacion = AddNewsToColectors($arr_news,$arr_colectores,$fechaInicio,$fechaFin,$version);
	foreach($arr_asociacion as $estado => $arr_resultado)
	{
		foreach($arr_resultado as $key => $value)
		{
			$idNews = $arr_resultado[$key]["id_news"];
			$nombreNews = $arr_resultado[$key]["name_news"];
			$idColector = $arr_resultado[$key]["id_colector"];
			$nombreColector = $arr_resultado[$key]["name_colector"];

			if ($estado == 'correctos')
			{
				$xml_correctos .= "<nodocorrecto id='".$idNews."' name='".$nombreNews."' idcolector='".$idColector."' namecolector='".$nombreColector."' />";
			}
			else
			{
				$xml_incorrectos .= "<nodoincorrecto id='".$idNews."' name='".$nombreNews."' idcolector='".$idColector."' namecolector='".$nombreColector."' />";
			}
		}//cierre foreach $arr_resultado
	}//cierre foreach $arr_asociacion

	$xml_correctos .= "</nodoscorrectos>";

	$xml_incorrectos .= "</nodosincorrectos>";
	$this->str_xml .= $xml_correctos . $xml_incorrectos;
	return $this->getXMLFinal();
}

//Method which obtain the collectors associated to the section passed as param
function getColectores($nodeID){

	$obj_colector = new menu_colector($nodeID,0);
	$this->str_xml .= $obj_colector->mostrar_colectores_xml();
	$resultado = $this->getXMLFinal();

	return \Ximdex\Utils\String::stripslashes( $resultado);

}

//Method which delete the tag passed as param, both opening and closing
function removeTags($tag_name){

	$tag_ini = '<' . $tag_name . '>';
	$tag_fin = '</' . $tag_name . '>';
	$this->str_xml = str_replace ($tag_ini, '', $this->str_xml);
	$this->str_xml = str_replace ($tag_fin, '', $this->str_xml);

}

//Method which add the beginning and end tags to xml
function getXMLFinal(){

	 $resultado = "<respuesta>" . \Ximdex\Utils\String::stripslashes( $this->str_xml) . "</respuesta>";
	 return $resultado;
}

//Method which saves in an array the xml tag containt passed as param
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
	//At the begging, answer is add that the beginning and at the end of the xml, to avoid an error creating the XML object
  //and it's deleted because more things are going to be added to the XML string
	$this->removeTags('respuesta');
	return $arr_ids;
}

function getAtributosEspeciales($atributo){

	$this->str_xml = $this->getXMLFinal();

	$domDoc = new DOMDocument();
	$domDoc->validateOnParse = true;
	$domDoc->preserveWhiteSpace = false;
	$domDoc->loadXML($this->str_xml);
	$atributos_especiales = $domDoc->getElementsByTagname('colectores');
	$resultado = $atributos_especiales[0]->getAttributeNode($atributo);

	$this->removeTags('respuesta');
	return $resultado;
}

//Method which obtain the news last version
function getVersionNews($arr_colectors,$arr_news)
{
	$i=0;
	foreach ($arr_news as $newsID){
		foreach ($arr_colectors as $colectorID){
			$data = new datafactory($newsID);
			$lasVersion = $data->getLastVersion();
			$lasSubVersion = $data->getLastSubVersion($lasVersion);
			$version[$i] = $lasVersion . "-" . $lasSubVersion;
			$i++;
		}
	}
	return $version;
}

//Method which obtain the las version of news in collectors
function getVersionColector($arr_colectors,$arr_news)
{
	$i=0;

	foreach ($arr_news as $newsID){
		foreach ($arr_colectors as $colectorID){
			//Checking if the news has a version in collectior. If not, it is obtaind from the news last version
			$relNewsColector = new RelNewsColector();
			$idRel = $relNewsColector->hasNews($colectorID, $newsID);
			$version = NULL;
			if ($idRel > 0) {
				$relNewsColector = new RelNewsColector($idRel);
				$version = $relNewsColector->get('Version');
				$subversion = $relNewsColector->get('SubVersion');
			} else {
				XMD_Log::info(_('No relationship was found in the database'));
			}

			// This first case CANNOT BE GET unless the query fails or the relations didn't exist
			if (!$version) {
				$data = new datafactory($newsID);
				$lasVersion = $data->getLastVersion();
				$lasSubVersion = $data->getLastSubVersion($lasVersion);
				$ultimaVersion = $lasVersion. "-" .$lasSubVersion;
			} else {
				$ultimaVersion = sprintf("%s-%s", $version, $subversion);
			}
			$version[$i] = $ultimaVersion;
			$i++;
		}
	}
	return $version;
}

}//End of class
?>
